<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JobApplicationController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $CurrentUser */
        $CurrentUser = Auth::user();

        // Base query scoped strictly to the logged-in user
        $UserQuery = $CurrentUser->JobApplications();

        // Fetch top 10 recent applications ordered by created_at DESC
        $RecentApplications = (clone $UserQuery)->orderBy('created_at', 'DESC')->take(10)->get();

        // 1. KPI Metrics
        $TotalApplications = (clone $UserQuery)->count();

        // Counts unique application posts that have at least 1 interview logged or are in an interviewing status
        $InterviewsDone = (clone $UserQuery)
            ->where(function ($Query) {
                $Query->whereHas('Interviews')
                      ->orWhereIn('status', ['Currently Interviewing', 'Interviewing', 'Interview Done', 'Interviews Done', 'Exam done']);
            })
            ->count();

        // Individual standard status counts
        $AppliedCount          = (clone $UserQuery)->where('status', 'Applied')->count();
        $UnderReviewCount      = (clone $UserQuery)->where('status', 'Under Review')->count();
        $CurrentlyInterviewing = (clone $UserQuery)->where('status', 'Currently Interviewing')->count();
        $HiredCount            = (clone $UserQuery)->where('status', 'Hired')->count();
        $RejectedCount         = (clone $UserQuery)->where('status', 'Rejected')->count();
        $WaitingResponse       = (clone $UserQuery)->where('status', 'Waiting response')->count();

        // Dynamic grouping for all status values (including user-created custom statuses)
        $StatusCounts = (clone $UserQuery)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // 2. Chart Aggregations
        $ApplicationsBySource = (clone $UserQuery)
            ->select('source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->pluck('count', 'source');

        $ApplicationsByJobType = (clone $UserQuery)
            ->select('job_type', DB::raw('count(*) as count'))
            ->whereNotNull('job_type')
            ->groupBy('job_type')
            ->pluck('count', 'job_type');

        $ApplicationsByLocation = (clone $UserQuery)
            ->select('city', DB::raw('count(*) as count'))
            ->groupBy('city')
            ->pluck('count', 'city');

        $ApplicationsByDate = (clone $UserQuery)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->pluck('count', 'date');

        return view('job_applications.index', compact(
            'RecentApplications',
            'TotalApplications',
            'InterviewsDone',
            'AppliedCount',
            'UnderReviewCount',
            'CurrentlyInterviewing',
            'HiredCount',
            'RejectedCount',
            'WaitingResponse',
            'StatusCounts',
            'ApplicationsBySource',
            'ApplicationsByJobType',
            'ApplicationsByLocation',
            'ApplicationsByDate'
        ));
    }

    public function applications()
{
    $JobApplications = JobApplication::with('interviews')
        ->orderByDesc('created_at')
        ->get();

    // Add these two lines:
    $CustomStatuses = \App\Models\ApplicationStatus::orderBy('name')->pluck('name')->all();
    $CustomSources  = \App\Models\ApplicationSource::orderBy('name')->pluck('name')->all();

    return view('job_applications.applications', compact(
        'JobApplications',
        'CustomStatuses',
        'CustomSources'
    ));
}

    public function create()
    {
        /** @var \App\Models\User $CurrentUser */
        $CurrentUser = Auth::user();

        $DefaultSources = [
            'BossJob', 'Career Fair', 'Career Page', 'Company Website',
            'Direct', 'Facebook', 'Indeed', 'JobStreet', 'JobTarget',
            'LinkedIn', 'OnlineJobs.ph (OLJ)', 'Prosple', 'Referral'
        ];
        $UserSources = $CurrentUser->sources()->pluck('name')->toArray();
        $CustomSources = array_values(array_unique(array_merge($DefaultSources, $UserSources)));

        $DefaultStatuses = [
            'Applied', 'Under Review', 'Currently Interviewing',
            'Waiting response', 'Offered', 'Hired', 'Rejected', 'No Response'
        ];
        $UserStatuses = $CurrentUser->statuses()->pluck('name')->toArray();
        $CustomStatuses = array_values(array_unique(array_merge($DefaultStatuses, $UserStatuses)));

        return view('job_applications.create', compact('CustomSources', 'CustomStatuses'));
    }

    public function store(Request $Request)
    {
        $ValidatedData = $Request->validate([
            'company_name'   => 'required|string|max:255',
            'position'       => 'required|string|max:255',
            'job_type'       => 'required|string|max:255',
            'salary'         => 'required_if:job_type,Remote|nullable|numeric|min:0',
            'salary_min'     => 'required_unless:job_type,Remote|nullable|numeric|min:0',
            'salary_max'     => 'required_unless:job_type,Remote|nullable|numeric|gte:salary_min',
            'currency'       => 'required|string|max:10',
            'pay_rate_type'  => 'required|in:Monthly,Hourly',
            'hours_per_week' => 'nullable|numeric|min:1|max:168',
            'description'    => 'required|string',
            'pros'           => 'nullable|string|max:255',
            'cons'           => 'nullable|string|max:255',
            'city'           => 'required|string|max:255',
            'ApplicationURL' => 'required|url|max:255',
            'source'         => 'required|string|max:255',
            'status'         => 'required|string|max:255',
        ]);

        /** @var \App\Models\User $CurrentUser */
        $CurrentUser = Auth::user();
        $CurrentUser->JobApplications()->create($ValidatedData);

        return redirect()->route('job-applications.index')
                         ->with('success', 'Job application recorded successfully.');
    }

    public function destroy(JobApplication $JobApplication)
    {
        if ($JobApplication->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $JobApplication->delete();

        return redirect()->route('job-applications.applications')
                         ->with('success', 'Job application deleted.');
    }

    public function Import(Request $Request)
    {
        $Request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $File = $Request->file('csv_file');
        $Handle = fopen($File->getRealPath(), 'r');
        
        $Header = fgetcsv($Handle, 2000, ',');
        if (!$Header) {
            return redirect()->back()->with('error', 'The uploaded CSV file is empty.');
        }

        $Header = array_map(function($Col) {
            return trim(preg_replace('/[\x00-\x1F\x7F\xEF\xBB\xBF]/', '', $Col));
        }, $Header);

        $ImportedCount = 0;
        /** @var \App\Models\User $CurrentUser */
        $CurrentUser = Auth::user();

        while (($Row = fgetcsv($Handle, 2000, ',')) !== false) {
            if (empty(array_filter($Row))) {
                continue;
            }

            $HeaderCount = count($Header);
            $RowCount    = count($Row);

            if ($RowCount < $HeaderCount) {
                $Row = array_pad($Row, $HeaderCount, '');
            } elseif ($RowCount > $HeaderCount) {
                $Row = array_slice($Row, 0, $HeaderCount);
            }

            $RowData = array_combine($Header, array_map('trim', $Row));

            $CompanyName = $RowData['company_name'] ?? null;
            $Position    = $RowData['position'] ?? null;

            if (empty($CompanyName) || empty($Position)) {
                continue;
            }

            $CurrentUser->JobApplications()->create([
                'company_name'   => $CompanyName,
                'position'       => $Position,
                'job_type'       => !empty($RowData['job_type']) ? $RowData['job_type'] : 'Onsite',
                'city'           => !empty($RowData['city']) ? $RowData['city'] : 'N/A',
                'salary'         => is_numeric($RowData['salary'] ?? null) ? $RowData['salary'] : null,
                'salary_min'     => is_numeric($RowData['salary_min'] ?? null) ? $RowData['salary_min'] : null,
                'salary_max'     => is_numeric($RowData['salary_max'] ?? null) ? $RowData['salary_max'] : null,
                'currency'       => !empty($RowData['currency']) ? $RowData['currency'] : 'PHP',
                'pay_rate_type'  => !empty($RowData['pay_rate_type']) ? $RowData['pay_rate_type'] : 'Monthly',
                'hours_per_week' => is_numeric($RowData['hours_per_week'] ?? null) ? $RowData['hours_per_week'] : 40,
                'ApplicationURL' => !empty($RowData['ApplicationURL']) ? $RowData['ApplicationURL'] : 'https://example.com',
                'source'         => !empty($RowData['source']) ? $RowData['source'] : 'Direct',
                'status'         => !empty($RowData['status']) ? $RowData['status'] : 'Applied',
                'description'    => !empty($RowData['description']) ? $RowData['description'] : 'Imported application record',
                'pros'           => !empty($RowData['pros']) ? $RowData['pros'] : null,
                'cons'           => !empty($RowData['cons']) ? $RowData['cons'] : null,
                'created_at'     => !empty($RowData['created_at']) ? $RowData['created_at'] : now(),
            ]);

            $ImportedCount++;
        }

        fclose($Handle);

        return redirect()->route('job-applications.applications')
                         ->with('success', "Successfully imported {$ImportedCount} job applications.");
    }

    public function Edit(JobApplication $JobApplication)
    {
        if ($JobApplication->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        /** @var \App\Models\User $CurrentUser */
        $CurrentUser = Auth::user();

        $DefaultSources = [
            'BossJob', 'Career Fair', 'Career Page', 'Company Website',
            'Direct', 'Facebook', 'Indeed', 'JobStreet', 'JobTarget',
            'LinkedIn', 'OnlineJobs.ph (OLJ)', 'Prosple', 'Referral'
        ];
        $UserSources = $CurrentUser->sources()->pluck('name')->toArray();
        $CustomSources = array_merge($DefaultSources, $UserSources);
        if ($JobApplication->source && !in_array($JobApplication->source, $CustomSources)) {
            $CustomSources[] = $JobApplication->source;
        }
        $CustomSources = array_values(array_unique($CustomSources));

        $DefaultStatuses = [
            'Applied', 'Under Review', 'Currently Interviewing',
            'Waiting response', 'Offered', 'Hired', 'Rejected', 'No Response'
        ];
        $UserStatuses = $CurrentUser->statuses()->pluck('name')->toArray();
        $CustomStatuses = array_merge($DefaultStatuses, $UserStatuses);
        if ($JobApplication->status && !in_array($JobApplication->status, $CustomStatuses)) {
            $CustomStatuses[] = $JobApplication->status;
        }
        $CustomStatuses = array_values(array_unique($CustomStatuses));

        return view('job_applications.edit', compact('JobApplication', 'CustomSources', 'CustomStatuses'));
    }

    public function Update(Request $Request, JobApplication $JobApplication)
    {
        if ($JobApplication->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $ValidatedData = $Request->validate([
            'company_name'   => 'required|string|max:255',
            'position'       => 'required|string|max:255',
            'job_type'       => 'required|string|max:255',
            'salary'         => 'required_if:job_type,Remote|nullable|numeric|min:0',
            'salary_min'     => 'required_unless:job_type,Remote|nullable|numeric|min:0',
            'salary_max'     => 'required_unless:job_type,Remote|nullable|numeric|gte:salary_min',
            'currency'       => 'required|string|max:10',
            'pay_rate_type'  => 'required|in:Monthly,Hourly',
            'hours_per_week' => 'nullable|numeric|min:1|max:168',
            'description'    => 'required|string',
            'pros'           => 'nullable|string|max:255',
            'cons'           => 'nullable|string|max:255',
            'city'           => 'required|string|max:255',
            'ApplicationURL' => 'required|url|max:255',
            'source'         => 'required|string|max:255',
            'status'         => 'required|string|max:255',
        ]);

        $JobApplication->update($ValidatedData);

        return redirect()->route('job-applications.applications')
                         ->with('success', 'Job application updated successfully.');
    }
}