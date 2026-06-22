<?php

declare (strict_types = 1);

namespace App\Services\Download;

use App\Services\Settings\SettingsService;

final class DownloadSettings
{
    public function __construct(private readonly SettingsService $settings)
    {}

    public function retentionMinutes(): int
    {
        return (int) $this->settings->get('retention_minutes', config('downloader.retention_minutes'));
    }

    public function maxFilesizeMb(): int
    {
        return (int) $this->settings->get('max_filesize_mb', config('downloader.max_filesize_mb'));
    }

    public function maxBatchItems(): int
    {
        return (int) $this->settings->get('max_batch_items', config('downloader.max_batch_items'));
    }

    public function cookiesEnabled(): bool
    {
        return (bool) $this->settings->get('enable_cookies', true);
    }
}
