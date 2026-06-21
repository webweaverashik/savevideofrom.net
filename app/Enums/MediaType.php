<?php

declare (strict_types = 1);

namespace App\Enums;

enum MediaType: string {
    case Video = 'video';
    case Audio = 'audio';
    case Image = 'image';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
