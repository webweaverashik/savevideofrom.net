<?php

declare(strict_types=1);

namespace App\Services\Download;

use App\Enums\DownloadStatus;
use App\Models\DownloadBatch;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class BatchZipper
{
    public function zip(DownloadBatch $batch): void
    {
        $children = $batch->jobs()->where('status', DownloadStatus::Completed)->get();

        if ($children->isEmpty()) {
            $batch->markFailed('None of the playlist items could be downloaded.');
            return;
        }

        $base   = rtrim((string) config('downloader.storage_path'), '/\\');
        $zipDir = $base . DIRECTORY_SEPARATOR . 'batches';
        if (! is_dir($zipDir)) {
            mkdir($zipDir, 0775, true);
        }
        $zipAbs = $zipDir . DIRECTORY_SEPARATOR . $batch->uuid . '.zip';

        $zip = new ZipArchive();
        if ($zip->open($zipAbs, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $batch->markFailed('Could not create the archive.');
            return;
        }

        $index = 1;
        foreach ($children as $child) {
            $abs = $base . DIRECTORY_SEPARATOR . $child->file_path;
            if ($child->file_path && is_file($abs)) {
                $zip->addFile($abs, sprintf('%02d-%s', $index, $child->file_name));
                $index++;
            }
        }
        $zip->close();

        if (! is_file($zipAbs)) {
            $batch->markFailed('Archive creation failed.');
            return;
        }

        $batch->update([
            'status'     => DownloadStatus::Completed,
            'zip_path'   => 'batches/' . $batch->uuid . '.zip',
            'zip_size'   => filesize($zipAbs),
            'expires_at' => now()->addMinutes((int) config('downloader.retention_minutes')),
        ]);

        // Files are now archived — reclaim the space.
        foreach ($children as $child) {
            $abs = $base . DIRECTORY_SEPARATOR . $child->file_path;
            if ($child->file_path && is_file($abs)) {
                @unlink($abs);
            }
        }

        Log::info('Batch archived', ['batch' => $batch->uuid, 'items' => $children->count()]);
    }
}