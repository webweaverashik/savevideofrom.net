<?php

declare (strict_types = 1);

namespace App\DataTransferObjects;

use App\Enums\MediaType;

final readonly class MediaFormatDTO
{
    public function __construct(
        public string $formatId,
        public string $ext,
        public MediaType $type,
        public ?string $quality = null,    // "1080p", "128kbps"
        public ?string $resolution = null, // "1920x1080"
        public ?int $filesize = null,      // bytes
        public ?int $fps = null,
        public ?string $vcodec = null,
        public ?string $acodec = null,
        public bool $hasVideo = false,
        public bool $hasAudio = false,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            formatId: (string) $data['format_id'],
            ext: (string) $data['ext'],
            type: MediaType::from($data['type'] ?? 'video'),
            quality: $data['quality'] ?? null,
            resolution: $data['resolution'] ?? null,
            filesize: isset($data['filesize']) ? (int) $data['filesize'] : null,
            fps: isset($data['fps']) ? (int) $data['fps'] : null,
            vcodec: $data['vcodec'] ?? null,
            acodec: $data['acodec'] ?? null,
            hasVideo: (bool) ($data['has_video'] ?? false),
            hasAudio: (bool) ($data['has_audio'] ?? false),
        );
    }

    public function toArray(): array
    {
        return [
            'format_id'  => $this->formatId,
            'ext'        => $this->ext,
            'type'       => $this->type->value,
            'quality'    => $this->quality,
            'resolution' => $this->resolution,
            'filesize'   => $this->filesize,
            'fps'        => $this->fps,
            'vcodec'     => $this->vcodec,
            'acodec'     => $this->acodec,
            'has_video'  => $this->hasVideo,
            'has_audio'  => $this->hasAudio,
        ];
    }
}
