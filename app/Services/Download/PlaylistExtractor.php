<?php

declare(strict_types=1);

namespace App\Services\Download;

class PlaylistExtractor
{
    public function __construct(
        private readonly PythonProcessRunner $runner,
        private readonly SsrfGuard $guard,
        private readonly PlatformDetector $detector,
        private readonly CookieResolver $cookies,
    ) {}

    /** @return array{title:string,platform:?string,count:int,entries:array<int,array>} */
    public function extract(string $url): array
    {
        $this->guard->assertSafe($url);

        $platform = $this->detector->detect($url);

        $result = $this->runner->run(
            config('downloader.scripts.playlist'),
            [
                'url'           => $url,
                'platform'      => $platform,
                'cookies_files' => $this->cookies->poolFor($platform),
            ],
            (int) config('downloader.extract_timeout'),
        );

        $result['platform'] = $platform;

        return $result;
    }
}