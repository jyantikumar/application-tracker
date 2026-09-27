<?php

namespace App\Http\Controllers;

use App\Models\ApplicationSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ApplicationSourceController extends Controller
{
    public function index()
    {
        return Inertia::render('Sources', [
            'sources' => Auth::user()
                ->sources()
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('application_sources', 'name')->where('user_id', Auth::id()),
            ],
        ], [
            'name.unique' => 'You already have a source with that name.',
        ]);

        $source = Auth::user()->sources()->create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'id'   => $source->id,
                'name' => $source->name,
            ], 201);
        }

        return back()->with('success', 'Source added successfully.');
    }

    public function update(Request $request, ApplicationSource $source)
    {
        abort_if($source->user_id !== Auth::id(), 403, 'Unauthorized action.');

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('application_sources', 'name')
                    ->where('user_id', Auth::id())
                    ->ignore($source->id),
            ],
        ], [
            'name.unique' => 'You already have a source with that name.',
        ]);

        $source->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'id'   => $source->id,
                'name' => $source->name,
            ]);
        }

        return back()->with('success', 'Source updated successfully.');
    }

    public function destroy(Request $request, ApplicationSource $source)
    {
        abort_if($source->user_id !== Auth::id(), 403, 'Unauthorized action.');

        $source->delete();

        if ($request->wantsJson()) {
            return response()->json(['deleted' => true]);
        }

        return back()->with('success', 'Source deleted.');
    }
}