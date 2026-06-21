<?php

declare (strict_types = 1);

namespace App\Services\Settings;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    private const CACHE_PREFIX = 'setting:';

    public function get(string $key, mixed $default = null): mixed
    {
        $setting = Cache::rememberForever(
            self::CACHE_PREFIX . $key,
            fn() => SiteSetting::where('key', $key)->first()
        );

        return $setting?->typedValue() ?? $default;
    }

    public function set(string $key, mixed $value, string $type = 'text', string $group = 'general'): void
    {
        if ($type === 'json' && is_array($value)) {
            $value = json_encode($value);
        }

        if ($type === 'boolean') {
            $value = $value ? '1' : '0';
        }

        SiteSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group],
        );

        Cache::forget(self::CACHE_PREFIX . $key);
    }

    /**
     * @return array<string, mixed>
     */
    public function group(string $group): array
    {
        return SiteSetting::where('group', $group)
            ->orderBy('order')
            ->get()
            ->mapWithKeys(fn(SiteSetting $s) => [$s->key => $s->typedValue()])
            ->all();
    }

    public function forget(string $key): void
    {
        Cache::forget(self::CACHE_PREFIX . $key);
    }

    public function flush(): void
    {
        SiteSetting::pluck('key')->each(fn(string $key) => $this->forget($key));
    }
}
