<?php

declare(strict_types=1);

namespace App\Services\Download;

use App\Services\Settings\SettingsService;

class CookieResolver
{
    public function __construct(
        private readonly SettingsService $settings,
        private readonly CookieStore $store,
    ) {}

    /** Best cookie for a job: user-supplied first, then per-platform admin cookie. */
    public function resolveForJob(?string $uuid, ?string $platform): ?string
    {
        if (! $this->settings->get('enable_cookies', true)) {
            return null;
        }

        if ($uuid !== null && $this->store->exists($uuid)) {
            return $this->store->path($uuid);
        }

        return $this->resolvePlatform($platform);
    }

    /** Per-platform admin cookie file (managed via the Phase 7 admin UI). */
    public function resolvePlatform(?string $platform): ?string
    {
        if ($platform === null) {
            return null;
        }

        $path = rtrim((string) config('downloader.cookies_path'), '/\\')
            . DIRECTORY_SEPARATOR . $platform . '.txt';

        return is_file($path) && is_readable($path) ? $path : null;
    }
}