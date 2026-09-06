<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobApplicationController extends Controller
{
    public function index()
    {
        // Fetch only top 10 recent applications for the Dashboard view
        $RecentApplications = JobApplication::latest()->take(10)->get();

        // 1. KPI Metrics Calculations
        $TotalApplications     = JobApplication::count();
        $InterviewsDone        = JobApplication::whereIn('status', ['Interviewing', 'Interview Done', 'Interviews Done'])->count();
        $AppliedCount          = JobApplication::where('status', 'Applied')->count();
        $UnderReviewCount      = JobApplication::where('status', 'Under Review')->count();
        $CurrentlyInterviewing = JobApplication::where('status', 'Currently Interviewing')->count();
        $HiredCount            = JobApplication::where('status', 'Hired')->count();
        $RejectedCount         = JobApplication::where('status', 'Rejected')->count();
        $WaitingResponse       = JobApplication::where('status', 'Waiting response')->count();

        // 2. Chart Aggregations
        $ApplicationsBySource = JobApplication::select('source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->pluck('count', 'source');

        $ApplicationsByJobType = JobApplication::select('job_type', DB::raw('count(*) as count'))
    ->whereNotNull('job_type')
    ->groupBy('job_type')
    ->pluck('count', 'job_type');

        $ApplicationsByLocation = JobApplication::select('city', DB::raw('count(*) as count'))
            ->groupBy('city')
            ->pluck('count', 'city');

        $ApplicationsByDate = JobApplication::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
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
            'ApplicationsBySource',
            'ApplicationsByJobType',
            'ApplicationsByLocation',
            'ApplicationsByDate'
        ));
    }

    public function applications()
    {
        $JobApplications = JobApplication::latest()->get();
        return view('job_applications.applications', compact('JobApplications'));
    }

    public function create()
    {
        return view('job_applications.create');
    }

    public function store(Request $Request)
    {
        $ValidatedData = $Request->validate([
    'company_name'   => 'required|string|max:255',
    'position'       => 'required|string|max:255',
    'salary'         => 'required|numeric|min:0',
    'currency'       => 'required|string|max:10',
    'pay_rate_type'  => 'required|in:Monthly,Hourly',
    'hours_per_week' => 'nullable|numeric|min:1|max:168',
    'job_type'       => 'required|string|max:255',
    'description'    => 'required|string',
    'pros'           => 'nullable|string|max:255',
    'cons'           => 'nullable|string|max:255',
    'city'           => 'required|string|max:255',
    'ApplicationURL' => 'required|url|max:255',
    'source'         => 'required|string|max:255',
    'status'         => 'required|string|max:255',
]);

        JobApplication::create($ValidatedData);

        return redirect()->route('job-applications.index')
                         ->with('success', 'Job application recorded successfully.');
    }

    public function edit(JobApplication $JobApplication)
    {
        return view('job_applications.edit', compact('JobApplication'));
    }

    public function update(Request $Request, JobApplication $JobApplication)
    {
        $ValidatedData = $Request->validate([
    'company_name'   => 'required|string|max:255',
    'position'       => 'required|string|max:255',
    'salary'         => 'required|numeric|min:0',
    'currency'       => 'required|string|max:10',
    'pay_rate_type'  => 'required|in:Monthly,Hourly',
    'hours_per_week' => 'nullable|numeric|min:1|max:168',
    'job_type'       => 'required|string|max:255',
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

    public function destroy(JobApplication $JobApplication)
    {
        $JobApplication->delete();

        return redirect()->route('job-applications.applications')
                         ->with('success', 'Job application deleted.');
    }
}