<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function Store(Request $Request, JobApplication $JobApplication)
    {
        $ValidatedData = $Request->validate([
            'interview_type' => 'required|string|max:255',
            'scheduled_at'   => 'required|date',
            'status'         => 'required|in:Scheduled,Completed,Cancelled',
            'notes'          => 'nullable|string',
        ]);

        $JobApplication->Interviews()->create($ValidatedData);

        // Automatically update main application status if needed
        if ($ValidatedData['status'] === 'Completed') {
            $JobApplication->update(['status' => 'Interviewing']);
        }

        return redirect()->back()->with('success', 'Interview log recorded successfully.');
    }

    public function Update(Request $Request, Interview $Interview)
    {
        $ValidatedData = $Request->validate([
            'interview_type' => 'required|string|max:255',
            'scheduled_at'   => 'required|date',
            'status'         => 'required|in:Scheduled,Completed,Cancelled',
            'notes'          => 'nullable|string',
        ]);

        $Interview->update($ValidatedData);

        return redirect()->back()->with('success', 'Interview updated successfully.');
    }

    public function Destroy(Interview $Interview)
    {
        $Interview->delete();

        return redirect()->back()->with('success', 'Interview log removed.');
    }
}