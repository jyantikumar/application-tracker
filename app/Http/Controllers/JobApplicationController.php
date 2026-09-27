<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\ApplicationStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class JobApplicationController extends Controller
{
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
            ->get();

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
        $jobApplications = JobApplication::with('interviews')
            ->where('user_id', $userId)
            ->latest()
            ->get();

        $customStatuses = ApplicationStatus::where('user_id', $userId)->pluck('name');

        return Inertia::render('JobApplications/Applications', [
            'JobApplications' => $jobApplications,
            'CustomStatuses'  => $customStatuses,
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('job-applications.applications');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name'   => 'required|string|max:255',
            'position'       => 'required|string|max:255',
            'job_type'       => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:255',
            'source'         => 'nullable|string|max:255',
            'status'         => 'required|string|max:255',
            'currency'       => 'nullable|string|max:10',
            'salary'         => 'nullable|numeric',
            'salary_min'     => 'nullable|numeric',
            'salary_max'     => 'nullable|numeric',
            'pay_rate_type'  => 'nullable|string|max:50',
            'hours_per_week' => 'nullable|numeric',
            'description'    => 'nullable|string',
            'pros'           => 'nullable|string',
            'cons'           => 'nullable|string',
            'ApplicationURL' => 'nullable|url',
        ]);

        $validated['user_id'] = auth()->id();
        JobApplication::create($validated);

        return redirect()->back()->with('success', 'Application created successfully.');
    }

    public function edit(JobApplication $jobApplication): RedirectResponse
    {
        return redirect()->route('job-applications.applications');
    }

    public function update(Request $request, JobApplication $jobApplication): RedirectResponse
    {
        $validated = $request->validate([
            'company_name'   => 'required|string|max:255',
            'position'       => 'required|string|max:255',
            'job_type'       => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:255',
            'source'         => 'nullable|string|max:255',
            'status'         => 'required|string|max:255',
            'currency'       => 'nullable|string|max:10',
            'salary'         => 'nullable|numeric',
            'salary_min'     => 'nullable|numeric',
            'salary_max'     => 'nullable|numeric',
            'pay_rate_type'  => 'nullable|string|max:50',
            'hours_per_week' => 'nullable|numeric',
            'description'    => 'nullable|string',
            'pros'           => 'nullable|string',
            'cons'           => 'nullable|string',
            'ApplicationURL' => 'nullable|url',
        ]);

        $jobApplication->update($validated);

        return redirect()->back()->with('success', 'Application updated successfully.');
    }

    public function destroy(JobApplication $jobApplication): RedirectResponse
    {
        $jobApplication->delete();
        return redirect()->back()->with('success', 'Application deleted successfully.');
    }
}