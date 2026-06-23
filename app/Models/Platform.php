<?php

declare (strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    protected $fillable = [
        'slug', 'page_slug', 'name', 'domain_patterns', 'icon', 'color',
        'is_active', 'is_featured', 'is_published', 'sort_order',
        'meta_title', 'meta_description', 'h1', 'intro', 'card_description', 'howto', 'faqs', 'howto', 'faqs',
    ];

    protected function casts(): array
    {
        return [
            'domain_patterns' => 'array',
            'howto'           => 'array',
            'faqs'            => 'array',
            'is_active'       => 'boolean',
            'is_featured'     => 'boolean',
            'is_published'    => 'boolean',
            'sort_order'      => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function landingUrl(): ?string
    {
        return $this->page_slug ? route('landing', $this->page_slug) : null;
    }

    public function matchesHost(string $host): bool
    {
        $host = strtolower($host);
        foreach ((array) $this->domain_patterns as $pattern) {
            if ($host === $pattern || str_ends_with($host, '.' . $pattern)) {
                return true;
            }
        }
        return false;
    }
}
