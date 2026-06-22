<?php

declare (strict_types = 1);

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        $platforms = [
            ['slug' => 'youtube', 'name' => 'YouTube', 'patterns' => ['youtube.com', 'youtu.be'], 'color' => '#FF0000', 'featured' => true],
            ['slug' => 'facebook', 'name' => 'Facebook', 'patterns' => ['facebook.com', 'fb.watch', 'fb.com'], 'color' => '#1877F2', 'featured' => true],
            ['slug' => 'instagram', 'name' => 'Instagram', 'patterns' => ['instagram.com'], 'color' => '#E1306C', 'featured' => true],
            ['slug' => 'tiktok', 'name' => 'TikTok', 'patterns' => ['tiktok.com'], 'color' => '#EE1D52', 'featured' => true],
            ['slug' => 'twitter', 'name' => 'X (Twitter)', 'patterns' => ['twitter.com', 'x.com'], 'color' => '#1D9BF0', 'featured' => true],
            ['slug' => 'reddit', 'name' => 'Reddit', 'patterns' => ['reddit.com'], 'color' => '#FF4500', 'featured' => true],
            ['slug' => 'linkedin', 'name' => 'LinkedIn', 'patterns' => ['linkedin.com'], 'color' => '#0A66C2', 'featured' => false],
            ['slug' => 'vimeo', 'name' => 'Vimeo', 'patterns' => ['vimeo.com'], 'color' => '#1AB7EA', 'featured' => false],
            ['slug' => 'dailymotion', 'name' => 'Dailymotion', 'patterns' => ['dailymotion.com', 'dai.ly'], 'color' => '#0066DC', 'featured' => false],
            ['slug' => 'pinterest', 'name' => 'Pinterest', 'patterns' => ['pinterest.com', 'pin.it'], 'color' => '#E60023', 'featured' => false],
            ['slug' => 'threads', 'name' => 'Threads', 'patterns' => ['threads.net'], 'color' => '#000000', 'featured' => false],
        ];

        foreach ($platforms as $i => $p) {
            $name = $p['name'];

            Platform::updateOrCreate(['slug' => $p['slug']], [
                'name'            => $name,
                'page_slug'       => $p['slug'] . '-video-downloader',
                'domain_patterns' => $p['patterns'],
                'color'           => $p['color'],
                'is_active'       => true,
                'is_featured'     => $p['featured'],
                'is_published'    => true,
                'sort_order'      => $i,
                'meta_title'      => "{$name} Video Downloader — Download {$name} Videos in HD, Free",
                'meta_description' => "Download {$name} videos online for free. Paste a link, pick a quality up to 4K or grab MP3 audio, and save in seconds — no sign-up, no app.",
                'h1' => "{$name} Video Downloader",
                'intro' => "Save any public {$name} video to your device in seconds. Paste the link, choose a quality from 144p up to 4K, or pull the audio as MP3 — completely free, no account, and it works on phones, tablets, and desktop.",
                'howto' => [
                    ['title' => "Copy the {$name} link", 'text' => "Open the {$name} video and copy its URL from the address bar or the share menu."],
                    ['title' => 'Paste and fetch', 'text' => 'Paste the link into the box above and let SaveVideoFrom load the available formats.'],
                    ['title' => 'Pick a format and download', 'text' => 'Choose a video quality or MP3 audio, and your file downloads automatically.'],
                ],
                'faqs' => [
                    ['q' => "Is the {$name} downloader free?", 'a' => "Yes — downloading {$name} videos is completely free, with no limits and no registration."],
                    ['q' => "What quality can I download {$name} videos in?", 'a' => "Any quality the original offers, from 144p up to 4K where available, plus MP3 audio extraction."],
                    ['q' => 'Do I need an app or account?', 'a' => 'No. It runs in your browser on any device — nothing to install and no sign-up.'],
                    ['q' => "Can I download private {$name} videos?", 'a' => "Only content you have permission to access. Please respect {$name}'s terms of service and copyright."],
                ],
            ]);
        }
    }
}
