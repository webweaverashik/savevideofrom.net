<?php

declare(strict_types=1);

namespace App\Services\Download;

use App\DataTransferObjects\MediaInfoDTO;
use App\Services\Download\Contracts\MediaExtractor;

class YtDlpExtractor implements MediaExtractor
{
    public function __construct(
        private readonly PythonProcessRunner $runner,
        private readonly SsrfGuard $guard,
        private readonly PlatformDetector $detector,
        private readonly CookieResolver $cookies,
    ) {}

    public function extract(string $url): MediaInfoDTO
    {
        $this->guard->assertSafe($url);

        $platform = $this->detector->detect($url);

        $result = $this->runner->run(
            config('downloader.scripts.extractor'),
            [
                'url'          => $url,
                'platform'     => $platform,
                'ffmpeg_path'  => config('downloader.ffmpeg_path') ?: null,
                'cookies_file' => $this->cookies->resolve($platform),
            ],
            (int) config('downloader.extract_timeout'),
        );

        $result['platform'] = $platform;

        return MediaInfoDTO::fromArray($result);
    }
}