<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class ExtractionException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly string $errorType = 'download_error',
        public readonly bool $retryable = true,
    ) {
        parent::__construct($message);
    }
}