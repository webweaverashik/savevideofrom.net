<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        $platforms = [
            ['slug' => 'youtube',     'name' => 'YouTube',     'patterns' => ['youtube.com', 'youtu.be'],       'color' => '#FF0000', 'featured' => true],
            ['slug' => 'facebook',    'name' => 'Facebook',    'patterns' => ['facebook.com', 'fb.watch', 'fb.com'], 'color' => '#1877F2', 'featured' => true],
            ['slug' => 'instagram',   'name' => 'Instagram',   'patterns' => ['instagram.com'],                 'color' => '#E1306C', 'featured' => true],
            ['slug' => 'tiktok',      'name' => 'TikTok',      'patterns' => ['tiktok.com'],                    'color' => '#000000', 'featured' => true],
            ['slug' => 'twitter',     'name' => 'X (Twitter)', 'patterns' => ['twitter.com', 'x.com'],          'color' => '#000000', 'featured' => true],
            ['slug' => 'reddit',      'name' => 'Reddit',      'patterns' => ['reddit.com'],                    'color' => '#FF4500', 'featured' => true],
            ['slug' => 'linkedin',    'name' => 'LinkedIn',    'patterns' => ['linkedin.com'],                  'color' => '#0A66C2', 'featured' => false],
            ['slug' => 'vimeo',       'name' => 'Vimeo',       'patterns' => ['vimeo.com'],                     'color' => '#1AB7EA', 'featured' => false],
            ['slug' => 'dailymotion', 'name' => 'Dailymotion', 'patterns' => ['dailymotion.com', 'dai.ly'],     'color' => '#0066DC', 'featured' => false],
            ['slug' => 'pinterest',   'name' => 'Pinterest',   'patterns' => ['pinterest.com', 'pin.it'],       'color' => '#E60023', 'featured' => false],
            ['slug' => 'threads',     'name' => 'Threads',     'patterns' => ['threads.net'],                   'color' => '#000000', 'featured' => false],
        ];

        foreach ($platforms as $i => $p) {
            Platform::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'name'            => $p['name'],
                    'domain_patterns' => $p['patterns'],
                    'color'           => $p['color'],
                    'is_active'       => true,
                    'is_featured'     => $p['featured'],
                    'sort_order'      => $i,
                ],
            );
        }
    }
}