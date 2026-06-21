<?php

declare (strict_types = 1);

namespace App\Services\Download\Contracts;

use App\Models\DownloadJob;

interface MediaDownloader
{
    /**
     * Download the media for a job and persist the resulting file
     * details back onto the model. Implemented in Phase 3.
     *
     * @throws \RuntimeException on download failure
     */
    public function download(DownloadJob $job): void;
}
