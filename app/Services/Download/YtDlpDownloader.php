<?php

declare (strict_types = 1);

namespace App\Services\Download;

use App\Enums\MediaType;
use App\Exceptions\BlockedContentException;
use App\Exceptions\ExtractionException;
use App\Exceptions\UnsafeUrlException;
use App\Models\DownloadJob;
use App\Services\Download\Contracts\MediaDownloader;

class YtDlpDownloader implements MediaDownloader
{
    public function __construct(
        private readonly PythonProcessRunner $runner,
        private readonly SsrfGuard $guard,
        private readonly CookieResolver $cookies,
        private readonly DownloadSettings $settings,
    ) {}

    public function download(DownloadJob $job): void
    {
        try {
            $this->guard->assertSafe($url);
        } catch (BlockedContentException $e) {
            throw new ExtractionException($e->getMessage(), 'blocked', false);
        } catch (UnsafeUrlException $e) {
            throw new ExtractionException($e->getMessage(), 'unsafe_url', false);
        }

        $outputDir = rtrim((string) config('downloader.storage_path'), '/\\')
        . DIRECTORY_SEPARATOR . $job->uuid;

        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0775, true);
        }

        $audioOnly = $job->media_type === MediaType::Audio;

        $result = $this->runner->run(
            config('downloader.scripts.downloader'),
            [
                'url'              => $job->url,
                'output_dir'       => $outputDir,
                'format_id'        => $job->format_id,
                'quality'          => $job->requested_quality,
                'requested_format' => $job->requested_format,
                'media_type'       => $job->media_type?->value ?? 'video',
                'audio_only'       => $audioOnly,
                'max_filesize_mb'  => $this->settings->maxFilesizeMb(),
                'ffmpeg_path'      => config('downloader.ffmpeg_path') ?: null,
                'cookies_files'    => $this->cookies->poolFor($job->platform),
            ],
            (int) config('downloader.process_timeout'),
        );

        $job->markCompleted([
            'file_name'  => $result['file_name'],
            'file_path'  => $job->uuid . '/' . $result['file_name'],
            'file_size'  => $result['file_size'] ?? null,
            'mime_type'  => $result['mime_type'] ?? null,
            'media_type' => MediaType::from($result['media_type'] ?? 'video'),
            'title'      => $job->title ?: ($result['title'] ?? null),
        ]);
    }
}
