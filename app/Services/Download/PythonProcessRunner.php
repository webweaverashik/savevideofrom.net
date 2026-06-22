<?php

declare (strict_types = 1);

namespace App\Services\Download;

use App\Exceptions\ExtractionException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class PythonProcessRunner
{
    /**
     * Run a Python worker script, sending $payload as JSON on stdin and
     * returning the decoded success JSON. Throws on any failure.
     *
     * @throws ExtractionException
     */
    public function run(string $script, array $payload, int $timeout): array
    {
        if (! is_file($script)) {
            throw new ExtractionException("Worker script not found: {$script}", 'engine_error', false);
        }

        $process = new Process(
            command: [config('downloader.python_path'), $script],
            cwd: dirname($script),
        );
        $process->setTimeout($timeout);
        $process->setInput((string) json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        try {
            Log::debug('Spawning Python worker', [
                'script'      => basename($script),
                'ffmpeg_path' => $payload['ffmpeg_path'] ?? null,
            ]);

            $process->run();
        } catch (ProcessTimedOutException) {
            throw new ExtractionException('The download timed out. Please try again.', 'network_error', true);
        }

        $stdout = trim($process->getOutput());
        $stderr = $process->getErrorOutput();

        $json = $this->parseJson($stdout);

        if ($json === null) {
            Log::error('Python worker returned no JSON', [
                'script' => basename($script),
                'exit'   => $process->getExitCode(),
                'stderr' => Str::limit($stderr, 1000),
                'stdout' => Str::limit($stdout, 1000),
            ]);
            throw new ExtractionException('The download engine returned an unexpected response.', 'engine_error', true);
        }

        if (! ($json['success'] ?? false)) {
            Log::warning('Python worker reported an error', [
                'script'     => basename($script),
                'error_type' => $json['error_type'] ?? null,
                'stderr'     => Str::limit($stderr, 1500),
            ]);

            throw new ExtractionException(
                $json['error'] ?? 'Unknown error',
                $json['error_type'] ?? 'download_error',
                (bool) ($json['retryable'] ?? true),
            );
        }

        return $json;
    }

    private function parseJson(string $output): ?array
    {
        $decoded = json_decode($output, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Fallback: scan for the last JSON object line (defensive).
        foreach (array_reverse(preg_split("/\r\n|\r|\n/", $output) ?: []) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $decoded = json_decode($line, true);
            if (is_array($decoded) && (isset($decoded['success']) || isset($decoded['error']))) {
                return $decoded;
            }
        }

        return null;
    }
}
