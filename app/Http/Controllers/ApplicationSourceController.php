<?php

namespace App\Http\Controllers;

use App\Models\ApplicationSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ApplicationSourceController extends Controller
{
    public function index()
    {
        $Sources = Auth::user()->sources()->orderBy('name')->get();

        return view('job_applications.sources', compact('Sources'));
    }

    public function store(Request $Request)
    {
        $Validated = $Request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('application_sources', 'name')->where('user_id', Auth::id()),
            ],
        ], [
            'name.unique' => 'You already have a source with that name.',
        ]);

        Auth::user()->sources()->create($Validated);

        return redirect()->route('sources.index')->with('success', 'Source added successfully.');
    }

    public function update(Request $Request, ApplicationSource $Source)
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

        return redirect()->route('sources.index')->with('success', 'Source updated successfully.');
    }

    public function destroy(ApplicationSource $Source)
    {
        if ($Source->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $Source->delete();

        return redirect()->route('sources.index')->with('success', 'Source deleted.');
    }
}