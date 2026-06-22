<?php

declare(strict_types=1);

namespace App\Services\Download;

use Illuminate\Http\UploadedFile;

class CookieManager
{
    private function baseDir(): string
    {
        return rtrim((string) config('downloader.cookies_path'), '/\\');
    }

    public function platformDir(string $platform): string
    {
        return $this->baseDir() . DIRECTORY_SEPARATOR . $platform;
    }

    public function count(string $platform): int
    {
        $dir = $this->platformDir($platform);
        return is_dir($dir) ? count(glob($dir . DIRECTORY_SEPARATOR . '*.txt') ?: []) : 0;
    }

    /** @return array<int, array{name:string,size:int,modified:string,valid:bool}> */
    public function list(string $platform): array
    {
        $dir = $this->platformDir($platform);
        if (! is_dir($dir)) {
            return [];
        }

        $out = [];
        foreach (glob($dir . DIRECTORY_SEPARATOR . '*.txt') ?: [] as $file) {
            clearstatcache(true, $file);
            $size = (int) filesize($file);
            $out[] = [
                'name'     => basename($file),
                'size'     => $size,
                'modified' => date('Y-m-d H:i', filemtime($file)),
                'valid'    => $size > 40 && is_readable($file),
            ];
        }

        usort($out, static fn ($a, $b) => strcmp($a['name'], $b['name']));

        return $out;
    }

    public function store(string $platform, UploadedFile $file): string
    {
        $dir = $this->platformDir($platform);
        if (! is_dir($dir)) {
            mkdir($dir, 0700, true);
        }

        $name = $this->nextName($dir);
        $path = $dir . DIRECTORY_SEPARATOR . $name;

        file_put_contents($path, $this->normalize((string) file_get_contents($file->getRealPath())));
        @chmod($path, 0600);

        return $name;
    }

    public function delete(string $platform, string $filename): bool
    {
        $name = basename($filename); // strip any path components
        if (! str_ends_with(strtolower($name), '.txt')) {
            return false;
        }

        $dir      = $this->platformDir($platform);
        $realDir  = realpath($dir) ?: '';
        $realFile = realpath($dir . DIRECTORY_SEPARATOR . $name) ?: '';

        // Confirm the resolved file actually sits inside the platform folder.
        if ($realDir === '' || $realFile === '' || ! str_starts_with($realFile, $realDir)) {
            return false;
        }

        return @unlink($realFile);
    }

    private function nextName(string $dir): string
    {
        $i = 1;
        while (file_exists($dir . DIRECTORY_SEPARATOR . "cookies_{$i}.txt")) {
            $i++;
        }
        return "cookies_{$i}.txt";
    }

    private function normalize(string $contents): string
    {
        return rtrim(str_replace(["\r\n", "\r"], "\n", trim($contents))) . "\n";
    }

    /** Lenient Netscape cookies.txt check: header line or tab-delimited cookie rows. */
    public static function looksLikeNetscape(string $contents): bool
    {
        $contents = trim($contents);
        if ($contents === '') {
            return false;
        }
        if (str_contains($contents, 'HTTP Cookie File')) {
            return true;
        }
        foreach (preg_split("/\r\n|\r|\n/", $contents) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (substr_count($line, "\t") >= 5) {
                return true;
            }
        }
        return false;
    }
}