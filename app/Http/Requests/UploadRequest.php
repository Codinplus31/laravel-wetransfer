<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'files' => 'required|array|min:1|max:5',
            'files.*' => 'required|file|max:102400|mimes:jpg,png,pdf,docx,zip',
            'expires_in' => 'nullable|integer|min:1',
            'email_to_notify' => 'nullable|email',
            'password' => 'nullable|string|min:6',
        ];
    }

    public function messages()
    {
        return [
            'files.required' => 'Please select at least one file to upload.',
            'files.max' => 'You can upload a maximum of 5 files at once.',
            'files.*.max' => 'Each file must not exceed 100MB.',
            'files.*.mimes' => 'Only jpg, png, pdf, docx, and zip files are allowed.',
        ];
    }
}
