<?php

declare (strict_types = 1);

namespace App\DataTransferObjects;

final readonly class MediaInfoDTO
{
    /**
     * @param MediaFormatDTO[] $formats
     */
    public function __construct(
        public string $title,
        public string $webpageUrl,
        public ?string $uploader = null,
        public ?string $thumbnail = null,
        public ?int $duration = null,
        public ?string $platform = null,
        public array $formats = [],
        public bool $isPlaylist = false,
    ) {}

    public static function fromArray(array $data): self
    {
        $formats = array_map(
            static fn(array $f) => MediaFormatDTO::fromArray($f),
            $data['formats'] ?? []
        );

        return new self(
            title: (string) ($data['title'] ?? 'Untitled'),
            webpageUrl: (string) ($data['webpage_url'] ?? $data['url'] ?? ''),
            uploader: $data['uploader'] ?? null,
            thumbnail: $data['thumbnail'] ?? null,
            duration: isset($data['duration']) ? (int) $data['duration'] : null,
            platform: $data['platform'] ?? null,
            formats: $formats,
            isPlaylist: (bool) ($data['is_playlist'] ?? false),
        );
    }

    public function toArray(): array
    {
        return [
            'title'       => $this->title,
            'webpage_url' => $this->webpageUrl,
            'uploader'    => $this->uploader,
            'thumbnail'   => $this->thumbnail,
            'duration'    => $this->duration,
            'platform'    => $this->platform,
            'is_playlist' => $this->isPlaylist,
            'formats'     => array_map(static fn(MediaFormatDTO $f) => $f->toArray(), $this->formats),
        ];
    }
}
