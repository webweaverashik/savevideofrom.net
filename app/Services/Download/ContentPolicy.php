<?php

declare (strict_types = 1);

namespace App\Services\Download;

use App\Services\Settings\SettingsService;

class ContentPolicy
{
    public function __construct(private readonly SettingsService $settings)
    {}

    /** @return string[] */
    private function keywords(): array
    {
        $custom = $this->settings->get('blocked_keywords');
        $list   = is_array($custom) && $custom ? $custom : (array) config('downloader.blocked_keywords');

        return array_values(array_filter(array_map(
            static fn($k) => strtolower(trim((string) $k)),
            $list
        )));
    }

    /** True if the text contains a blocked keyword as a whole word. */
    public function isBlocked(?string $text): bool
    {
        $text = strtolower((string) $text);
        if ($text === '') {
            return false;
        }

        foreach ($this->keywords() as $kw) {
            if ($kw === '') {
                continue;
            }
            // Whole-word match: avoids "sex" matching "essex", "xxx" matching codes, etc.
            if (preg_match('/(?<![a-z0-9])' . preg_quote($kw, '/') . '(?![a-z0-9])/i', $text)) {
                return true;
            }
        }

        return false;
    }
}
