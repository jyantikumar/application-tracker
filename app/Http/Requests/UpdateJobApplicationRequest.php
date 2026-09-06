<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Set to true to allow authorized execution
    }

    public function rules(): array
    {
        return [
            'company_name'   => 'required|string|max:255',
            'position'       => 'required|string|max:255',
            'salary'         => 'required|numeric|min:0',
            'job_type'       => 'required|string|max:255',
            'description'    => 'required|string',
            'pros'           => 'nullable|string|max:255',
            'cons'           => 'nullable|string|max:255',
            'city'           => 'required|string|max:255',
            'ApplicationURL' => 'required|url|max:255',
            'source'         => 'required|string|max:255',
            'status'         => 'required|string|max:255',
        ];
    }
}