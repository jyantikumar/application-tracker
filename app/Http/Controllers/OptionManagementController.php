<?php

namespace App\Http\Controllers;

use App\Models\ApplicationSource;
use App\Models\ApplicationStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class OptionManagementController extends Controller
{
    public function indexSources(): Response
    {
        $sources = ApplicationSource::where('user_id', auth()->id())->get();

        return Inertia::render('JobApplications/Sources', [
            'sources' => $sources,
        ]);
    }

    public function storeSource(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $validated['user_id'] = auth()->id();
        ApplicationSource::create($validated);

        return redirect()->back()->with('success', 'Source added successfully.');
    }

    public function updateSource(Request $request, ApplicationSource $Source): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $Source->update($validated);

        return redirect()->back()->with('success', 'Source updated successfully.');
    }

    public function destroySource(ApplicationSource $Source): RedirectResponse
    {
        $Source->delete();

        return redirect()->back()->with('success', 'Source deleted successfully.');
    }

    public function indexStatuses(): Response
    {
        $statuses = ApplicationStatus::where('user_id', auth()->id())->get();

        return Inertia::render('JobApplications/Statuses', [
            'statuses' => $statuses,
        ]);
    }

    public function storeStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
        ]);

        $validated['user_id'] = auth()->id();
        ApplicationStatus::create($validated);

        return redirect()->back()->with('success', 'Status added successfully.');
    }

    public function updateStatus(Request $request, ApplicationStatus $Status): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
        ]);

        $Status->update($validated);

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    public function destroyStatus(ApplicationStatus $Status): RedirectResponse
    {
        $Status->delete();

        return redirect()->back()->with('success', 'Status deleted successfully.');
    }
}