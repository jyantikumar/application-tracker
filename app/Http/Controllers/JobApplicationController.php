<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\ApplicationStatus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class JobApplicationController extends Controller
{
    /**
     * Shared validation rules for store/update.
     */
    protected function rules(): array
    {
        return [
            'company_name'   => 'required|string|max:255',
            'position'       => 'required|string|max:255',
            'job_type'       => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:255',
            'source'         => 'nullable|string|max:255',
            'status'         => 'required|string|max:255',
            'currency'       => 'nullable|string|max:10',
            'salary'         => 'nullable|numeric',
            'salary_min'     => 'nullable|numeric|min:0',
            'salary_max'     => 'nullable|numeric|min:0|gte:salary_min',
            'pay_rate_type'  => 'nullable|string|max:50',
            'hours_per_week' => 'nullable|numeric',
            'description'    => 'nullable|string',
            'pros'           => 'nullable|string',
            'cons'           => 'nullable|string',
            'ApplicationURL' => 'nullable|url',
            // 👇 Resume rules
            'resume'         => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:10240',
            'remove_resume'  => 'nullable|boolean',
        ];
    }

    /**
     * Derive midpoint salary when a min/max range is provided.
     */
    protected function normalizeCompensation(array $Validated): array
    {
        $Min = isset($Validated['salary_min']) && $Validated['salary_min'] !== ''
            ? (float) $Validated['salary_min']
            : null;
        $Max = isset($Validated['salary_max']) && $Validated['salary_max'] !== ''
            ? (float) $Validated['salary_max']
            : null;

        if ($Min !== null && $Max !== null) {
            $Validated['salary'] = round(($Min + $Max) / 2, 2);
        }

        return $Validated;
    }

    public function index(): Response
    {
        $userId = auth()->id();
        $applications = JobApplication::with('interviews')->where('user_id', $userId)->get();

        $statusCountsRaw = $applications->groupBy('status')->map->count();

        $applicationsBySource = $applications->groupBy('source')
            ->map->count()
            ->reject(fn ($count, $key) => empty($key));

        $applicationsByDate = $applications->groupBy(function ($app) {
            return $app->created_at ? $app->created_at->format('Y-m-d') : 'Unknown';
        })->map->count();

        $applicationsByJobType = $applications->groupBy('job_type')
            ->map->count()
            ->reject(fn ($count, $key) => empty($key));

        $applicationsByLocation = $applications->groupBy('city')
            ->map->count()
            ->reject(fn ($count, $key) => empty($key));

        $recentApplications = JobApplication::where('user_id', $userId)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($app) {
                $app->resume_url = $app->resume_path ? route('job-applications.resume', $app->id) : null;
                return $app;
            });

        return Inertia::render('JobApplications/Index', [
            'TotalApplications'     => $applications->count(),
            'InterviewsDone'        => $applications->filter(fn ($app) => $app->interviews->where('status', 'Completed')->count() > 0)->count(),
            'AppliedCount'          => $statusCountsRaw->get('Applied', 0),
            'UnderReviewCount'      => $statusCountsRaw->get('Under Review', 0),
            'CurrentlyInterviewing' => $statusCountsRaw->get('Currently Interviewing', 0),
            'HiredCount'            => $statusCountsRaw->get('Hired', 0),
            'RejectedCount'         => $statusCountsRaw->get('Rejected', 0),
            'WaitingResponse'       => $statusCountsRaw->get('Waiting response', 0),
            'StatusCounts'          => $statusCountsRaw->toArray(),
            'ApplicationsBySource'  => $applicationsBySource->toArray(),
            'ApplicationsByDate'    => $applicationsByDate->toArray(),
            'ApplicationsByJobType' => $applicationsByJobType->toArray(),
            'ApplicationsByLocation'=> $applicationsByLocation->toArray(),
            'RecentApplications'    => $recentApplications,
        ]);
    }

    public function applications(): Response
    {
        $userId = auth()->id();
        $jobApplications = JobApplication::with(['interviews', 'attachments'])
            ->where('user_id', $userId)
            ->latest()
            ->get()
            ->map(function ($app) {
                $app->resume_url = $app->resume_path ? route('job-applications.resume', $app->id) : null;
                return $app;
            });

        $customStatuses = ApplicationStatus::where('user_id', $userId)->pluck('name');

        return Inertia::render('JobApplications/Applications', [
            'JobApplications' => $jobApplications,
            'CustomStatuses'  => $customStatuses,
        ]);
    }

    /**
     * Stream or download the stored resume securely.
     */
    public function showResume(JobApplication $jobApplication)
    {
        abort_if($jobApplication->user_id !== auth()->id(), 403);

        if (!$jobApplication->resume_path || !Storage::disk('public')->exists($jobApplication->resume_path)) {
            abort(404, 'Resume file not found on server.');
        }

        $filePath = Storage::disk('public')->path($jobApplication->resume_path);
        $mimeType = Storage::disk('public')->mimeType($jobApplication->resume_path) ?? 'application/pdf';

        return response()->file($filePath, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline; filename="' . ($jobApplication->resume_original_name ?? 'resume.pdf') . '"',
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('job-applications.applications');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated = $this->normalizeCompensation($validated);

        unset($validated['remove_resume']);
        $validated['user_id'] = auth()->id();

        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
            $validated['resume_path'] = $file->store("resumes/{$validated['user_id']}", 'public');
            $validated['resume_original_name'] = $file->getClientOriginalName();
        }

        unset($validated['resume']); // don't try to save the UploadedFile instance

        JobApplication::create($validated);

        return redirect()->back()->with('success', 'Application created successfully.');
    }

    public function edit(JobApplication $jobApplication): RedirectResponse
    {
        return redirect()->route('job-applications.applications');
    }

    public function update(Request $request, JobApplication $jobApplication): RedirectResponse
    {
        abort_if($jobApplication->user_id !== auth()->id(), 403);

        $validated = $request->validate($this->rules());
        $validated = $this->normalizeCompensation($validated);

        // 👇 Handle remove_resume flag
        if (!empty($validated['remove_resume']) && $jobApplication->resume_path) {
            Storage::disk('public')->delete($jobApplication->resume_path);
            $validated['resume_path'] = null;
            $validated['resume_original_name'] = null;
        }

        // 👇 Handle replacement
        if ($request->hasFile('resume')) {
            if ($jobApplication->resume_path) {
                Storage::disk('public')->delete($jobApplication->resume_path);
            }
            $file = $request->file('resume');
            $validated['resume_path'] = $file->store("resumes/{$jobApplication->user_id}", 'public');
            $validated['resume_original_name'] = $file->getClientOriginalName();
        }

        unset($validated['resume'], $validated['remove_resume']);

        $jobApplication->update($validated);

        return redirect()->back()->with('success', 'Application updated successfully.');
    }

    public function destroy(JobApplication $jobApplication): RedirectResponse
    {
        abort_if($jobApplication->user_id !== auth()->id(), 403);

        // 👇 Clean up stored resume file
        if ($jobApplication->resume_path) {
            Storage::disk('public')->delete($jobApplication->resume_path);
        }

        $jobApplication->delete();

        return redirect()->back()->with('success', 'Application deleted successfully.');
    }
}