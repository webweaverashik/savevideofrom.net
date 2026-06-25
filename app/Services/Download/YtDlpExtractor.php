<?php

declare (strict_types = 1);

namespace App\Services\Download;

use App\DataTransferObjects\MediaInfoDTO;
use App\Exceptions\BlockedContentException;
use App\Exceptions\ExtractionException;
use App\Exceptions\UnsafeUrlException;
use App\Services\Download\Contracts\MediaExtractor;

class YtDlpExtractor implements MediaExtractor
{
    public function __construct(
        private readonly PythonProcessRunner $runner,
        private readonly SsrfGuard $guard,
        private readonly PlatformDetector $detector,
    ) {}

    public function extract(string $url, array $cookieFiles = []): MediaInfoDTO
    {
        try {
            $this->guard->assertSafe($url);
        } catch (BlockedContentException $e) {
            throw new ExtractionException($e->getMessage(), 'blocked', false);
        } catch (UnsafeUrlException $e) {
            throw new ExtractionException($e->getMessage(), 'unsafe_url', false);
        }

        $platform = $this->detector->detect($url);

        $result = $this->runner->run(
            config('downloader.scripts.extractor'),
            [
                'url'           => $url,
                'platform'      => $platform,
                'ffmpeg_path'   => config('downloader.ffmpeg_path') ?: null,
                'cookies_files' => array_values($cookieFiles),
            ],
            (int) config('downloader.extract_timeout'),
        );

        $result['platform'] = $platform;

        return MediaInfoDTO::fromArray($result);
    }
}
