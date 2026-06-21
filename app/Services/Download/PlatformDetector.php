<?php

declare (strict_types = 1);

namespace App\Services\Download;

class PlatformDetector
{
    /**
     * Host fragment => canonical platform slug.
     */
    private const MAP = [
        'youtube.com'     => 'youtube',
        'youtu.be'        => 'youtube',
        'facebook.com'    => 'facebook',
        'fb.watch'        => 'facebook',
        'fb.com'          => 'facebook',
        'instagram.com'   => 'instagram',
        'tiktok.com'      => 'tiktok',
        'twitter.com'     => 'twitter',
        'x.com'           => 'twitter',
        'reddit.com'      => 'reddit',
        'vimeo.com'       => 'vimeo',
        'dailymotion.com' => 'dailymotion',
        'dai.ly'          => 'dailymotion',
        'pinterest.com'   => 'pinterest',
        'pin.it'          => 'pinterest',
        'threads.net'     => 'threads',
        'linkedin.com'    => 'linkedin',
        'twitch.tv'       => 'twitch',
        'soundcloud.com'  => 'soundcloud',
    ];

    public function detect(string $url): ?string
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if ($host === '') {
            return null;
        }

        $host = preg_replace('/^www\./', '', $host);

        foreach (self::MAP as $needle => $slug) {
            if ($host === $needle || str_ends_with($host, '.' . $needle)) {
                return $slug;
            }
        }

        // Fallback: second-level domain as a generic slug (e.g. "example").
        $parts = explode('.', $host);
        $count = count($parts);

        return $count >= 2 ? $parts[$count - 2] : $host;
    }
}
