<?php

declare (strict_types = 1);

namespace App\Jobs;

use App\Enums\DownloadStatus;
use App\Models\DownloadBatch;
use App\Models\DownloadJob as DownloadJobModel;
use App\Services\Download\CookieStore;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanupExpiredDownloadsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(CookieStore $store)
    {
        $disk    = config('downloader.disk');
        $deleted = 0;

        DownloadJobModel::expired()
            ->whereNotNull('file_path')
            ->chunkById(200, function ($jobs) use ($disk, &$deleted): void {
                foreach ($jobs as $job) {
                    if ($job->file_path && Storage::disk($disk)->exists($job->file_path)) {
                        Storage::disk($disk)->delete($job->file_path);

                        if ($job->has_cookies) {
                            $store->delete($job->uuid);
                        }
                    }

                    $job->update([
                        'status'    => DownloadStatus::Expired,
                        'file_path' => null,
                    ]);

                    $deleted++;
                }
            });

        if ($deleted > 0) {
            Log::info('Expired downloads cleaned up', ['count' => $deleted]);
        }

        DownloadBatch::expired()
            ->where('status', '!=', DownloadStatus::Expired)
            ->chunkById(100, function ($batches) use ($disk): void {
                foreach ($batches as $batch) {
                    if ($batch->zip_path && $disk->exists($batch->zip_path)) {
                        $disk->delete($batch->zip_path);
                    }
                    $batch->update([
                        'status'   => DownloadStatus::Expired,
                        'zip_path' => null,
                    ]);
                }
            });
    }
}
