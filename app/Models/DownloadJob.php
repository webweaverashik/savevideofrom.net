<?php

declare (strict_types = 1);

namespace App\Models;

use App\Enums\DownloadStatus;
use App\Enums\MediaType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DownloadJob extends Model
{
    protected $fillable = [
        'uuid', 'batch_id', 'url', 'platform', 'has_cookies', 'status', 'media_type',
        'title', 'thumbnail_url', 'duration',
        'requested_format', 'requested_quality', 'format_id',
        'file_name', 'file_path', 'file_size', 'mime_type',
        'error_type', 'error_message',
        'ip_hash', 'user_agent', 'meta',
        'started_at', 'completed_at', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'has_cookies'  => 'boolean',
            'status'       => DownloadStatus::class,
            'media_type'   => MediaType::class,
            'meta'         => 'array',
            'duration'     => 'integer',
            'file_size'    => 'integer',
            'started_at'   => 'datetime',
            'completed_at' => 'datetime',
            'expires_at'   => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (DownloadJob $job): void {
            $job->uuid ??= (string) Str::uuid();
            $job->status ??= DownloadStatus::Pending;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function markProcessing(): void
    {
        $this->update([
            'status'     => DownloadStatus::Processing,
            'started_at' => now(),
        ]);
    }

    public function markCompleted(array $attributes = []): void
    {
        $this->update(array_merge($attributes, [
            'status'       => DownloadStatus::Completed,
            'completed_at' => now(),
            'expires_at'   => now()->addMinutes((int) config('downloader.retention_minutes')),
        ]));
    }

    public function markFailed(string $type, string $message): void
    {
        $this->update([
            'status'        => DownloadStatus::Failed,
            'error_type'    => $type,
            'error_message' => Str::limit($message, 1000, ''),
        ]);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expires_at')->where('expires_at', '<', now());
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(DownloadBatch::class, 'batch_id');
    }
}
