<?php

declare (strict_types = 1);

namespace App\Services\Download\Contracts;

use App\DataTransferObjects\MediaInfoDTO;

interface MediaExtractor
{
    /**
     * Run a metadata-only extraction (no file download) and return
     * available formats. Implemented in Phase 3 via the Python worker.
     *
     * @throws \RuntimeException on extraction failure
     */
    public function extract(string $url): MediaInfoDTO;
}
