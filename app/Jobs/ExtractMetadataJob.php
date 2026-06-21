<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\DownloadStatus;
use App\Models\DownloadJob as DownloadJobModel;
use App\Services\Download\Contracts\MediaExtractor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExtractMetadataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 10;

    public function __construct(public string $uuid) {}

    public function handle(MediaExtractor $extractor): void
    {
        $job = DownloadJobModel::where('uuid', $this->uuid)->first();
        if ($job === null) {
            return;
        }

        $job->update(['status' => DownloadStatus::Extracting]);

        $info = $extractor->extract($job->url);

        $job->update([
            'status'        => DownloadStatus::Ready,
            'title'         => $info->title,
            'thumbnail_url' => $info->thumbnail,
            'duration'      => $info->duration,
            'meta'          => $info->toArray(),
            'expires_at'    => now()->addMinutes((int) config('downloader.retention_minutes')),
        ]);
    }

    public function timeout(): int
    {
        return (int) config('downloader.extract_timeout', 60);
    }

    public function failed(Throwable $e): void
    {
        Log::warning('Metadata extraction failed', [
            'uuid'  => $this->uuid,
            'error' => $e->getMessage(),
        ]);

        DownloadJobModel::where('uuid', $this->uuid)
            ->first()
            ?->markFailed('extract_error', $e->getMessage());
    }
}