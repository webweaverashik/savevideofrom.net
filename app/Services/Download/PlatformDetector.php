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

        // Fallback: use the registrable domain label, never the TLD.
        $parts = array_values(array_filter(explode('.', $host)));
        $count = count($parts);

        if ($count >= 2) {
            // For host like "foo.co.uk" skip known 2-part TLDs; otherwise take the SLD.
            $twoPartTlds = ['co.uk', 'com.au', 'co.in', 'com.br', 'co.jp'];
            $lastTwo     = $parts[$count - 2] . '.' . $parts[$count - 1];
            $slug        = in_array($lastTwo, $twoPartTlds, true) && $count >= 3
                ? $parts[$count - 3]
                : $parts[$count - 2];
        } else {
            $slug = $host;
        }

        // Guard against degenerate slugs (TLDs, empties).
        $bad = ['com', 'net', 'org', 'www', 'co', 'io', 'tv', ''];
        return in_array($slug, $bad, true) ? 'other' : $slug;
    }
}
