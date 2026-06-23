<?php

declare (strict_types = 1);

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Avoid duplicate seeds on re-run.
        if (MenuItem::exists()) {
            return;
        }

        // ── Header ──────────────────────────────────────────────
        $header = [
            ['Home', '/'],
            ['YouTube', '/youtube-video-downloader'],
            ['TikTok', '/tiktok-video-downloader'],
            ['Instagram', '/instagram-video-downloader'],
            ['Facebook', '/facebook-video-downloader'],
        ];
        foreach ($header as $i => [$label, $url]) {
            MenuItem::create(['label' => $label, 'url' => $url, 'location' => 'header', 'sort_order' => $i]);
        }

        $more      = MenuItem::create(['label' => 'More', 'url' => null, 'location' => 'header', 'sort_order' => 99]);
        $moreLinks = [
            ['X (Twitter)', '/twitter-video-downloader'],
            ['Reddit', '/reddit-video-downloader'],
            ['Vimeo', '/vimeo-video-downloader'],
            ['Pinterest', '/pinterest-video-downloader'],
            ['LinkedIn', '/linkedin-video-downloader'],
            ['Threads', '/threads-video-downloader'],
            ['Dailymotion', '/dailymotion-video-downloader'],
        ];
        foreach ($moreLinks as $i => [$label, $url]) {
            MenuItem::create(['label' => $label, 'url' => $url, 'location' => 'header', 'parent_id' => $more->id, 'sort_order' => $i]);
        }

        // ── Footer columns ──────────────────────────────────────
        $downloaders = MenuItem::create(['label' => 'Downloaders', 'location' => 'footer', 'sort_order' => 0]);
        foreach ([
            ['YouTube Downloader', '/youtube-video-downloader'],
            ['Facebook Downloader', '/facebook-video-downloader'],
            ['Instagram Downloader', '/instagram-video-downloader'],
            ['TikTok Downloader', '/tiktok-video-downloader'],
            ['X (Twitter) Downloader', '/twitter-video-downloader'],
            ['Reddit Downloader', '/reddit-video-downloader'],
        ] as $i => [$label, $url]) {
            MenuItem::create(['label' => $label, 'url' => $url, 'location' => 'footer', 'parent_id' => $downloaders->id, 'sort_order' => $i]);
        }

        $company = MenuItem::create(['label' => 'Company', 'location' => 'footer', 'sort_order' => 1]);
        foreach ([
            ['Home', '/'],
            ['Sitemap', '/sitemap.xml'],
        ] as $i => [$label, $url]) {
            MenuItem::create(['label' => $label, 'url' => $url, 'location' => 'footer', 'parent_id' => $company->id, 'sort_order' => $i]);
        }

        $legal = MenuItem::create(['label' => 'Legal', 'location' => 'footer', 'sort_order' => 2]);
        foreach ([
            ['Privacy Policy', '#'],
            ['Terms of Service', '#'],
            ['Contact', '#'],
        ] as $i => [$label, $url]) {
            MenuItem::create(['label' => $label, 'url' => $url, 'location' => 'footer', 'parent_id' => $legal->id, 'sort_order' => $i]);
        }
    }
}
