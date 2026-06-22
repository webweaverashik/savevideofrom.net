<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\DownloadBatch;
use App\Services\Download\BatchZipper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FinalizeBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $batchUuid) {}

    public function handle(BatchZipper $zipper): void
    {
        $batch = DownloadBatch::where('uuid', $this->batchUuid)->first();
        if ($batch !== null) {
            $zipper->zip($batch);
        }
    }
}