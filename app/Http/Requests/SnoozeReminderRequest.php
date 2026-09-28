<?php

namespace App\Http\Requests;

use App\Models\Reminder;
use Illuminate\Foundation\Http\FormRequest;

class SnoozeReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $reminder = $this->route('reminder');

        if (! $reminder instanceof Reminder) {
            return false;
        }

        $reminder->loadMissing('application');

        return $reminder->application
            && $reminder->application->user_id === $this->user()?->id;
    }

    public function rules(): array
    {
        return [
            'hours' => ['nullable', 'integer', 'min:1', 'max:168'],
        ];
    }
}
