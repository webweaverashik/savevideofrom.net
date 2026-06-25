<?php

declare (strict_types = 1);

namespace App\Services\Download;

use App\Exceptions\UnsafeUrlException;

class SsrfGuard
{
    /**
     * Validate that a user-supplied URL is safe to hand to the extractor:
     * - http/https only, no embedded credentials
     * - host must resolve only to public, non-reserved IP addresses
     *
     * @throws UnsafeUrlException
     */
    public function assertSafe(string $url): void
    {
        $parts = parse_url($url);

        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            throw new UnsafeUrlException('Malformed URL.');
        }

        $scheme = strtolower($parts['scheme']);
        if (! in_array($scheme, (array) config('downloader.allowed_schemes'), true)) {
            throw new UnsafeUrlException('Only http and https URLs are allowed.');
        }

        if (isset($parts['user']) || isset($parts['pass'])) {
            throw new UnsafeUrlException('URLs with embedded credentials are not allowed.');
        }

        $host = $parts['host'];

        $this->assertAllowedContent($host);

        // If the host is a literal IP, validate it directly.
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $this->assertPublicIp($host);
            return;
        }

        $ips = $this->resolve($host);
        if ($ips === []) {
            throw new UnsafeUrlException('Could not resolve host.');
        }

        foreach ($ips as $ip) {
            $this->assertPublicIp($ip);
        }
    }

    /**
     * @return string[]
     */
    private function resolve(string $host): array
    {
        $ips = [];

        $a = @gethostbynamel($host);
        if (is_array($a)) {
            $ips = array_merge($ips, $a);
        }

        $aaaa = @dns_get_record($host, DNS_AAAA);
        if (is_array($aaaa)) {
            foreach ($aaaa as $record) {
                if (! empty($record['ipv6'])) {
                    $ips[] = $record['ipv6'];
                }
            }
        }

        return array_values(array_unique($ips));
    }

    private function assertPublicIp(string $ip): void
    {
        $isPublic = filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );

        // Explicitly block the cloud metadata endpoint.
        if ($isPublic === false || $ip === '169.254.169.254') {
            throw new UnsafeUrlException('URL resolves to a disallowed (private/reserved) address.');
        }
    }

    private function assertAllowedContent(string $host): void
    {
        $host = strtolower(preg_replace('/^www\./', '', $host));

        foreach ((array) config('downloader.blocked_hosts') as $blocked) {
            if ($host === $blocked || str_ends_with($host, '.' . $blocked)) {
                throw new \App\Exceptions\BlockedContentException(
                    'This site is not supported. Downloading adult or explicit content is prohibited.'
                );
            }
        }
    }
}
