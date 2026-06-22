<?php

declare(strict_types=1);

namespace App\Services\Download;

use App\Services\Settings\SettingsService;

class CookieResolver
{
    public function __construct(private readonly SettingsService $settings) {}

    /**
     * Return all readable cookie files for a platform's pool.
     * Empty when cookies are disabled, the platform is unknown,
     * or no folder/files exist. The worker tries public first,
     * then these in random order.
     *
     * @return string[] absolute cookie file paths
     */
    public function poolFor(?string $platform): array
    {
        if ($platform === null || ! $this->settings->get('enable_cookies', true)) {
            return [];
        }

        $dir = rtrim((string) config('downloader.cookies_path'), '/\\')
            . DIRECTORY_SEPARATOR . $platform;

        if (! is_dir($dir)) {
            return [];
        }

        $files = glob($dir . DIRECTORY_SEPARATOR . '*.txt') ?: [];

        return array_values(array_filter(
            $files,
            static fn (string $f) => is_file($f) && is_readable($f),
        ));
    }
}