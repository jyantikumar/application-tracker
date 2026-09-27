<?php

namespace App\Http\Controllers;

use App\Models\ApplicationStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ApplicationStatusController extends Controller
{
    public function index()
    {
        return Inertia::render('Statuses', [
            'statuses' => Auth::user()
                ->statuses()
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('application_statuses', 'name')->where('user_id', Auth::id()),
            ],
        ], [
            'name.unique' => 'You already have a status with that name.',
        ]);

        $status = Auth::user()->statuses()->create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'id'   => $status->id,
                'name' => $status->name,
            ], 201);
        }

        return back()->with('success', 'Status added successfully.');
    }

    public function update(Request $request, ApplicationStatus $status)
    {
        abort_if($status->user_id !== Auth::id(), 403, 'Unauthorized action.');

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('application_statuses', 'name')
                    ->where('user_id', Auth::id())
                    ->ignore($status->id),
            ],
        ], [
            'name.unique' => 'You already have a status with that name.',
        ]);

        $status->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'id'   => $status->id,
                'name' => $status->name,
            ]);
        }

        return back()->with('success', 'Status updated successfully.');
    }

    public function destroy(Request $request, ApplicationStatus $status)
    {
        abort_if($status->user_id !== Auth::id(), 403, 'Unauthorized action.');

        $status->delete();

        if ($request->wantsJson()) {
            return response()->json(['deleted' => true]);
        }

        return back()->with('success', 'Status deleted.');
    }
}