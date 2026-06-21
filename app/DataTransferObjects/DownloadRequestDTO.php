<?php

declare (strict_types = 1);

namespace App\DataTransferObjects;

use App\Enums\MediaType;

final readonly class DownloadRequestDTO
{
    public function __construct(
        public string $url,
        public MediaType $mediaType = MediaType::Video,
        public ?string $formatId = null,
        public ?string $quality = null,
        public bool $audioOnly = false,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            url: trim((string) $data['url']),
            mediaType: MediaType::from($data['media_type'] ?? 'video'),
            formatId: $data['format_id'] ?? null,
            quality: $data['quality'] ?? null,
            audioOnly: (bool) ($data['audio_only'] ?? false),
        );
    }
}
