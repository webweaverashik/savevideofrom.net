<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DownloadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'uuid'       => ['required', 'string', 'exists:download_jobs,uuid'],
            'media_type' => ['required', Rule::in(['video', 'audio'])],
            'format_id'  => ['nullable', 'string', 'max:60'],
            'quality'    => ['nullable', 'string', 'max:20'],
            'format'     => ['nullable', 'string', Rule::in(['mp4', 'webm', 'mkv', 'mp3', 'm4a', 'aac', 'wav'])],
        ];
    }
}