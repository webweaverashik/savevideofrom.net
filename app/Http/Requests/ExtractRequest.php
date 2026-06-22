<?php

declare (strict_types = 1);

namespace App\Http\Requests;

use App\Rules\NetscapeCookieFormat;
use Illuminate\Foundation\Http\FormRequest;

class ExtractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url'     => ['required', 'string', 'max:2048', 'url:http,https', new \App\Rules\SafeUrl()],
            'cookies' => ['nullable', 'string', 'max:102400', new NetscapeCookieFormat()],
        ];
    }

    public function messages(): array
    {
        return [
            'url.required' => 'Please paste a video link.',
            'url.url'      => 'That does not look like a valid link.',
        ];
    }
}
