<?php

declare(strict_types=1);

namespace App\Services\Download;

use App\Enums\MediaType;
use App\Models\DownloadJob;
use App\Services\Download\Contracts\MediaDownloader;

class YtDlpDownloader implements MediaDownloader
{
    public function __construct(
        private readonly PythonProcessRunner $runner,
        private readonly SsrfGuard $guard,
        private readonly CookieResolver $cookies,
    ) {}

    public function download(DownloadJob $job): void
    {
        $this->guard->assertSafe($job->url);

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
                'max_filesize_mb'  => (int) config('downloader.max_filesize_mb'),
                'ffmpeg_path'      => config('downloader.ffmpeg_path') ?: null,
                'cookies_file'     => $this->cookies->resolve($job->platform),
            ],
            (int) config('downloader.process_timeout'),
        );

        // Persist a path relative to the 'downloads' disk root: {uuid}/{file}.
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