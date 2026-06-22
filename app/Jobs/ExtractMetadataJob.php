<?php

declare (strict_types = 1);

namespace App\Jobs;

use App\Enums\DownloadStatus;
use App\Exceptions\ExtractionException;
use App\Models\DownloadJob as DownloadJobModel;
use App\Services\Download\Contracts\MediaExtractor;
use App\Services\Download\CookieResolver;
use App\Services\Download\DownloadSettings;
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

    public int $tries   = 2;
    public int $backoff = 10;

    public function __construct(public string $uuid)
    {}

    public function handle(MediaExtractor $extractor, CookieResolver $cookies, DownloadSettings $settings): void
    {
        $job = DownloadJobModel::where('uuid', $this->uuid)->first();
        if ($job === null) {
            return;
        }

        $job->update(['status' => DownloadStatus::Extracting]);

        try {
            $info = $extractor->extract($job->url, $cookies->poolFor($job->platform));
        } catch (ExtractionException $e) {
            if (! $e->retryable) {
                $job->markFailed($e->errorType, $e->getMessage());
                return;
            }
            throw $e;
        }

        $job->update([
            'status'        => DownloadStatus::Ready,
            'title'         => $info->title,
            'thumbnail_url' => $info->thumbnail,
            'duration'      => $info->duration,
            'platform'      => $info->platform,
            'meta'          => $info->toArray(),
            'expires_at'    => now()->addMinutes($settings->retentionMinutes()),
        ]);
    }

    public function timeout(): int
    {
        return (int) config('downloader.extract_timeout', 60);
    }

    public function failed(Throwable $e): void
    {
        Log::warning('Metadata extraction failed', ['uuid' => $this->uuid, 'error' => $e->getMessage()]);

        $type = $e instanceof ExtractionException ? $e->errorType : 'extract_error';

        DownloadJobModel::where('uuid', $this->uuid)->first()?->markFailed($type, $e->getMessage());
    }
}
