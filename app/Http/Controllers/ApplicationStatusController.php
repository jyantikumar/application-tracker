<?php

namespace App\Http\Controllers;

use App\Models\ApplicationStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ApplicationStatusController extends Controller
{
    public function index()
    {
        $Statuses = Auth::user()->statuses()->orderBy('name')->get();

        return view('job_applications.statuses', compact('Statuses'));
    }

    public function store(Request $Request)
    {
        $Validated = $Request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('application_statuses', 'name')->where('user_id', Auth::id()),
            ],
        ], [
            'name.unique' => 'You already have a status with that name.',
        ]);

        Auth::user()->statuses()->create($Validated);

        return redirect()->route('statuses.index')->with('success', 'Status added successfully.');
    }

    public function update(Request $Request, ApplicationStatus $Status)
    {
        if ($Status->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $Validated = $Request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('application_statuses', 'name')
                    ->where('user_id', Auth::id())
                    ->ignore($Status->id),
            ],
        ], [
            'name.unique' => 'You already have a status with that name.',
        ]);

        $Status->update($Validated);

        return redirect()->route('statuses.index')->with('success', 'Status updated successfully.');
    }

    public function destroy(ApplicationStatus $Status)
    {
        if ($Status->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $Status->delete();

        return redirect()->route('statuses.index')->with('success', 'Status deleted.');
    }
}