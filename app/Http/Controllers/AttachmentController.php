<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttachmentRequest;
use App\Models\Attachment;
use App\Models\JobApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    public function store(StoreAttachmentRequest $request, JobApplication $JobApplication): RedirectResponse
    {
        $file = $request->file('file');
        $path = $file->store(
            "attachments/{$JobApplication->user_id}/{$JobApplication->id}",
            'local'
        );

        $JobApplication->attachments()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $request->validated('file_type'),
            'file_size' => $file->getSize(),
        ]);

        return redirect()->back()->with('success', 'Attachment uploaded.');
    }

    public function download(Attachment $attachment): StreamedResponse
    {
        $this->authorizeAttachment($attachment);

        abort_unless(
            Storage::disk('local')->exists($attachment->file_path),
            404,
            'File not found.'
        );

        return Storage::disk('local')->download(
            $attachment->file_path,
            $attachment->file_name
        );
    }

    public function destroy(Attachment $attachment): RedirectResponse
    {
        $this->authorizeAttachment($attachment);

        $attachment->delete();

        return redirect()->back()->with('success', 'Attachment deleted.');
    }

    protected function authorizeAttachment(Attachment $attachment): void
    {
        $attachment->loadMissing('application');

        abort_if(
            ! $attachment->application || $attachment->application->user_id !== auth()->id(),
            403
        );
    }
}
