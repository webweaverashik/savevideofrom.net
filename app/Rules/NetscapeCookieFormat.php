<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\Download\CookieStore;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NetscapeCookieFormat implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! CookieStore::looksLikeNetscape((string) $value)) {
            $fail('The cookies must be in Netscape (cookies.txt) format.');
        }
    }
}