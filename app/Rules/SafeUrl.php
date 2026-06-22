<?php

declare(strict_types=1);

namespace App\Rules;

use App\Exceptions\UnsafeUrlException;
use App\Services\Download\SsrfGuard;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            app(SsrfGuard::class)->assertSafe((string) $value);
        } catch (UnsafeUrlException $e) {
            $fail($e->getMessage());
        }
    }
}