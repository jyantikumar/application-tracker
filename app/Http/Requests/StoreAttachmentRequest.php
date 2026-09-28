<?php

namespace App\Http\Requests;

use App\Models\Attachment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $application = $this->route('JobApplication');

        return $application && $application->user_id === $this->user()?->id;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:pdf,docx,txt', 'max:5120'],
            'file_type' => [
                'required',
                'string',
                Rule::in([
                    Attachment::TYPE_COVER_LETTER,
                    Attachment::TYPE_PORTFOLIO,
                    Attachment::TYPE_CERT,
                    Attachment::TYPE_OTHER,
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'Attachments must be a PDF, DOCX, or TXT file.',
            'file.max' => 'Attachments may not be larger than 5 MB.',
        ];
    }
}
