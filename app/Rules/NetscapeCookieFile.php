<?php

declare (strict_types = 1);

namespace App\Rules;

use App\Services\Download\CookieManager;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class NetscapeCookieFile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            $fail('The cookie file could not be read.');
            return;
        }

        $path = $value->getRealPath() ?: $value->getPathname();

        if (! $path || ! is_readable($path)) {
            $fail('The cookie file could not be read.');
            return;
        }

        $contents = (string) file_get_contents($path);

        if (! CookieManager::looksLikeNetscape($contents)) {
            $fail('One of the files is not a valid Netscape cookies.txt file.');
        }
    }
}
