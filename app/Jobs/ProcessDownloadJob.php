<?php

declare (strict_types = 1);

namespace App\Jobs;

use App\Exceptions\ExtractionException;
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

        try {
            $downloader->download($job); // persists file info + markCompleted on success
        } catch (ExtractionException $e) {
            if (! $e->retryable) {
                $job->markFailed($e->errorType, $e->getMessage());
                return;
            }
            throw $e;
        }
    }

    public function timeout(): int
    {
        return (int) config('downloader.process_timeout', 600);
    }

    public function failed(Throwable $e): void
    {
        Log::error('Download processing failed', ['uuid' => $this->uuid, 'error' => $e->getMessage()]);

        $type = $e instanceof ExtractionException ? $e->errorType : 'download_error';

        DownloadJobModel::where('uuid', $this->uuid)->first()?->markFailed($type, $e->getMessage());
    }
}
