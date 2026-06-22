<?php

declare (strict_types = 1);

namespace App\Services\Download\Contracts;

use App\DataTransferObjects\MediaInfoDTO;

interface MediaExtractor
{
    /**
     * @param string[] $cookieFiles platform cookie pool (worker tries public first, then these)
     */
    public function extract(string $url, array $cookieFiles = []): MediaInfoDTO;
}
