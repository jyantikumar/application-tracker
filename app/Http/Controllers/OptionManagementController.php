<?php

namespace App\Http\Controllers;

use App\Models\ApplicationSource;
use App\Models\ApplicationStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class OptionManagementController extends Controller
{
    public function indexSources(): Response
    {
        $sources = ApplicationSource::where('user_id', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('JobApplications/Sources', [
            'sources' => $sources,
        ]);
    }

    public function storeSource(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('application_sources', 'name')
                    ->where('user_id', auth()->id()),
            ],
        ], [
            'name.unique' => 'You already have a source with that name.',
        ]);

        $validated['user_id'] = auth()->id();
        $source = ApplicationSource::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'id'   => $source->id,
                'name' => $source->name,
            ], 201);
        }

        return redirect()->back()->with('success', 'Source added successfully.');
    }

    public function updateSource(Request $request, ApplicationSource $Source): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('application_sources', 'name')
                    ->where('user_id', auth()->id())
                    ->ignore($Source->id),
            ],
        ], [
            'name.unique' => 'You already have a source with that name.',
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
        $statuses = ApplicationStatus::where('user_id', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('JobApplications/Statuses', [
            'statuses' => $statuses,
        ]);
    }

    public function storeStatus(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('application_statuses', 'name')
                    ->where('user_id', auth()->id()),
            ],
            'color' => 'nullable|string|max:50',
        ], [
            'name.unique' => 'You already have a status with that name.',
        ]);

        $validated['user_id'] = auth()->id();
        $status = ApplicationStatus::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'id'   => $status->id,
                'name' => $status->name,
            ], 201);
        }

        return redirect()->back()->with('success', 'Status added successfully.');
    }

    public function updateStatus(Request $request, ApplicationStatus $Status): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('application_statuses', 'name')
                    ->where('user_id', auth()->id())
                    ->ignore($Status->id),
            ],
            'color' => 'nullable|string|max:50',
        ], [
            'name.unique' => 'You already have a status with that name.',
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