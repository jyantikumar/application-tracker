<?php

namespace App\Http\Controllers;

use App\Models\ApplicationSource;
use App\Models\ApplicationStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OptionManagementController extends Controller
{
    /* ============================================================
     |  SOURCES
     ============================================================ */

    public function indexSources()
    {
        /** @var \App\Models\User $CurrentUser */
        $CurrentUser = Auth::user();

        $Sources = $CurrentUser->sources()->orderBy('name')->get();

        return view('job_applications.sources', compact('Sources'));
    }

    public function storeSource(Request $Request)
    {
        $Validated = $Request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('application_sources', 'name')->where('user_id', Auth::id()),
            ],
        ], [
            'name.unique' => 'You already have a source with that name.',
        ]);

        /** @var \App\Models\User $CurrentUser */
        $CurrentUser = Auth::user();
        $CurrentUser->sources()->create($Validated);

        return redirect()->route('application-sources.index')
                         ->with('success', 'Source added successfully.');
    }

    public function updateSource(Request $Request, ApplicationSource $Source)
    {
        if ($Source->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $Validated = $Request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('application_sources', 'name')
                    ->where('user_id', Auth::id())
                    ->ignore($Source->id),
            ],
        ], [
            'name.unique' => 'You already have a source with that name.',
        ]);

        $Source->update($Validated);

        return redirect()->route('application-sources.index')
                         ->with('success', 'Source updated successfully.');
    }

    public function destroySource(ApplicationSource $Source)
    {
        if ($Source->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $Source->delete();

        return redirect()->route('application-sources.index')
                         ->with('success', 'Source deleted.');
    }

    /* ============================================================
     |  STATUSES
     ============================================================ */

    public function indexStatuses()
    {
        /** @var \App\Models\User $CurrentUser */
        $CurrentUser = Auth::user();

        $Statuses = $CurrentUser->statuses()->orderBy('name')->get();

        return view('job_applications.statuses', compact('Statuses'));
    }

    public function storeStatus(Request $Request)
    {
        $Validated = $Request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('application_statuses', 'name')->where('user_id', Auth::id()),
            ],
        ], [
            'name.unique' => 'You already have a status with that name.',
        ]);

        /** @var \App\Models\User $CurrentUser */
        $CurrentUser = Auth::user();
        $CurrentUser->statuses()->create($Validated);

        return redirect()->route('application-statuses.index')
                         ->with('success', 'Status added successfully.');
    }

    public function updateStatus(Request $Request, ApplicationStatus $Status)
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

        return redirect()->route('application-statuses.index')
                         ->with('success', 'Status updated successfully.');
    }

    public function destroyStatus(ApplicationStatus $Status)
    {
        if ($Status->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $Status->delete();

        return redirect()->route('application-statuses.index')
                         ->with('success', 'Status deleted.');
    }
}