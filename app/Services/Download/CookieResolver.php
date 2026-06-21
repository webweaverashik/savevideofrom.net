<?php

declare(strict_types=1);

namespace App\Services\Download;

use App\Services\Settings\SettingsService;

class CookieResolver
{
    public function __construct(private readonly SettingsService $settings) {}

    /**
     * Return a per-platform cookies file path if authenticated downloads
     * are enabled and a readable cookie file exists.
     */
    public function resolve(?string $platform): ?string
    {
        if ($platform === null || ! $this->settings->get('enable_cookies', true)) {
            return null;
        }

        $path = rtrim((string) config('downloader.cookies_path'), '/\\')
            . DIRECTORY_SEPARATOR . $platform . '.txt';

        return is_file($path) && is_readable($path) ? $path : null;
    }
}