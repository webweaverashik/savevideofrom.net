<?php

declare (strict_types = 1);

namespace App\Jobs;

use App\Models\DownloadJob as DownloadJobModel;
use App\Services\Download\Contracts\MediaDownloader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessDownloadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $backoff = 15;

    public function __construct(public string $uuid)
    {}

    public function handle(MediaDownloader $downloader): void
    {
        $job = DownloadJobModel::where('uuid', $this->uuid)->first();
        if ($job === null) {
            return;
        }

        $job->markProcessing();

        // The downloader persists file_name/file_path/file_size/mime_type
        // and calls markCompleted() on success (implemented in Phase 3).
        $downloader->download($job);
    }

    public function timeout(): int
    {
        return (int) config('downloader.process_timeout', 600);
    }

    public function failed(Throwable $e): void
    {
        Log::error('Download processing failed', [
            'uuid'  => $this->uuid,
            'error' => $e->getMessage(),
        ]);

        DownloadJobModel::where('uuid', $this->uuid)
            ->first()?->markFailed('download_error', $e->getMessage());
    }
}
