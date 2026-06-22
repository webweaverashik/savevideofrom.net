<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\SafeUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BatchDownloadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url'        => ['required', 'string', 'max:2048', 'url:http,https', new SafeUrl()],
            'title'      => ['nullable', 'string', 'max:255'],
            'urls'       => ['required', 'array', 'min:1', 'max:' . (int) config('downloader.max_batch_items')],
            'urls.*'     => ['required', 'string', 'url:http,https', 'max:2048'],
            'media_type' => ['required', Rule::in(['video', 'audio'])],
            'quality'    => ['nullable', 'string', 'max:20'],
            'format'     => ['nullable', 'string', Rule::in(['mp4', 'webm', 'mkv', 'mp3', 'm4a', 'aac', 'wav'])],
        ];
    }
}