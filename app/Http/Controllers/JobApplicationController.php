<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobApplicationController extends Controller
{
    public function index()
{
    // Fetch top 10 recent applications ordered by created_at DESC
    $RecentApplications = JobApplication::orderBy('created_at', 'DESC')->take(10)->get();

    // 1. KPI Metrics
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
        ->orderBy('date', 'DESC') // Sort chart dates descending
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
    // Fetch all records ordered by created_at DESC
    $JobApplications = JobApplication::with('Interviews')
        ->orderBy('created_at', 'DESC')
        ->get();

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
        'job_type'       => 'required|string|max:255',
        
        // Conditional compensation rules based on job setup
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

    JobApplication::create($ValidatedData);

    return redirect()->route('job-applications.index')
                     ->with('success', 'Job application recorded successfully.');
}
    public function destroy(JobApplication $JobApplication)
    {
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
    
    // Read header row
    $Header = fgetcsv($Handle, 2000, ',');
    if (!$Header) {
        return redirect()->back()->with('error', 'The uploaded CSV file is empty.');
    }

    // Clean BOM or invisible characters from header names
    $Header = array_map(function($Col) {
        return trim(preg_replace('/[\x00-\x1F\x7F\xEF\xBB\xBF]/', '', $Col));
    }, $Header);

    $ImportedCount = 0;

    while (($Row = fgetcsv($Handle, 2000, ',')) !== false) {
        // Skip empty rows
        if (empty(array_filter($Row))) {
            continue;
        }

        // Pad or trim row length to match header count
        $HeaderCount = count($Header);
        $RowCount    = count($Row);

        if ($RowCount < $HeaderCount) {
            $Row = array_pad($Row, $HeaderCount, '');
        } elseif ($RowCount > $HeaderCount) {
            $Row = array_slice($Row, 0, $HeaderCount);
        }

        $RowData = array_combine($Header, array_map('trim', $Row));

        // Required field validation
        $CompanyName = $RowData['company_name'] ?? null;
        $Position    = $RowData['position'] ?? null;

        if (empty($CompanyName) || empty($Position)) {
            continue;
        }

        JobApplication::create([
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
    return view('job_applications.edit', compact('JobApplication'));
}

public function Update(Request $Request, JobApplication $JobApplication)
{
    $ValidatedData = $Request->validate([
        'company_name'   => 'required|string|max:255',
        'position'       => 'required|string|max:255',
        'job_type'       => 'required|string|max:255',
        
        // Dynamic compensation validation matching store method
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