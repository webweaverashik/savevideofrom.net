<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DownloadStatus;
use App\Enums\MediaType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DownloadBatch extends Model
{
    protected $fillable = [
        'uuid', 'source_url', 'platform', 'title', 'status', 'total_items',
        'requested_quality', 'requested_format', 'media_type',
        'bus_batch_id', 'zip_path', 'zip_size', 'error_message', 'ip_hash', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'status'      => DownloadStatus::class,
            'media_type'  => MediaType::class,
            'total_items' => 'integer',
            'zip_size'    => 'integer',
            'expires_at'  => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (DownloadBatch $batch): void {
            $batch->uuid ??= (string) Str::uuid();
            $batch->status ??= DownloadStatus::Processing;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(DownloadJob::class, 'batch_id');
    }

    public function markFailed(string $message): void
    {
        $this->update([
            'status'        => DownloadStatus::Failed,
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
}