<?php

declare(strict_types=1);

namespace App\Services\Download;

class CookieStore
{
    private function dir(): string
    {
        return rtrim((string) config('downloader.cookies_path'), '/\\')
            . DIRECTORY_SEPARATOR . 'jobs';
    }

    public function path(string $uuid): string
    {
        return $this->dir() . DIRECTORY_SEPARATOR . $uuid . '.txt';
    }

    public function exists(string $uuid): bool
    {
        return is_file($this->path($uuid));
    }

    public function store(string $uuid, string $cookies): void
    {
        $dir = $this->dir();
        if (! is_dir($dir)) {
            mkdir($dir, 0700, true);
        }

        $file = $this->path($uuid);
        file_put_contents($file, $this->normalize($cookies));
        @chmod($file, 0600);
    }

    public function delete(string $uuid): void
    {
        $file = $this->path($uuid);
        if (is_file($file)) {
            @unlink($file);
        }
    }

    private function normalize(string $cookies): string
    {
        $cookies = str_replace(["\r\n", "\r"], "\n", trim($cookies));
        return $cookies . "\n";
    }

    /** Heuristic validation that the text is a Netscape cookies.txt. */
    public static function looksLikeNetscape(string $cookies): bool
    {
        $cookies = trim($cookies);
        if ($cookies === '') {
            return false;
        }

        if (str_contains($cookies, 'Netscape HTTP Cookie File') || str_contains($cookies, 'HTTP Cookie File')) {
            return true;
        }

        foreach (preg_split("/\r\n|\r|\n/", $cookies) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (substr_count($line, "\t") >= 6) {
                return true;
            }
        }

        return false;
    }
}