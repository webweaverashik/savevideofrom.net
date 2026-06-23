<?php

declare (strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'label', 'url', 'location', 'parent_id', 'sort_order', 'is_active', 'open_new_tab',
    ];

    protected function casts(): array
    {
        return [
            'is_active'    => 'boolean',
            'open_new_tab' => 'boolean',
            'sort_order'   => 'integer',
        ];
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** Active top-level items (with active children) for a location. */
    public static function tree(string $location): Collection
    {
        return static::query()
            ->where('location', $location)
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => fn($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();
    }
}
