<?php

declare (strict_types = 1);

namespace App\Enums;

enum DownloadStatus: string {
    case Pending    = 'pending';
    case Extracting = 'extracting';
    case Ready      = 'ready';
    case Processing = 'processing';
    case Completed  = 'completed';
    case Failed     = 'failed';
    case Expired    = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Pending    => 'Pending',
            self::Extracting => 'Extracting',
            self::Ready      => 'Ready',
            self::Processing => 'Processing',
            self::Completed  => 'Completed',
            self::Failed     => 'Failed',
            self::Expired    => 'Expired',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Completed, self::Failed, self::Expired], true);
    }

    public function isFailure(): bool
    {
        return in_array($this, [self::Failed, self::Expired], true);
    }
}
