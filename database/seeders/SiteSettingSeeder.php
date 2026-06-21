<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'site_name', 'value' => 'SaveVideoFrom.net', 'group' => 'general', 'label' => 'Site Name'],
            ['key' => 'tagline', 'value' => 'Free Universal Video Downloader', 'group' => 'general', 'label' => 'Tagline'],

            // SEO
            ['key' => 'default_meta_title', 'value' => 'SaveVideoFrom.net - Free Universal Video Downloader', 'group' => 'seo', 'label' => 'Default Meta Title'],
            ['key' => 'default_meta_description', 'value' => 'Download videos from YouTube, Facebook, Instagram, TikTok, X and more. Free, fast, no registration.', 'group' => 'seo', 'label' => 'Default Meta Description'],

            // Download
            ['key' => 'retention_minutes', 'value' => '60', 'type' => 'text', 'group' => 'download', 'label' => 'File Retention (minutes)'],
            ['key' => 'max_filesize_mb', 'value' => '2048', 'type' => 'text', 'group' => 'download', 'label' => 'Max File Size (MB)'],
            ['key' => 'enable_cookies', 'value' => '1', 'type' => 'boolean', 'group' => 'download', 'label' => 'Allow Authenticated Downloads'],
        ];

        foreach ($settings as $order => $s) {
            SiteSetting::updateOrCreate(
                ['key' => $s['key']],
                [
                    'value'       => $s['value'],
                    'type'        => $s['type'] ?? 'text',
                    'group'       => $s['group'],
                    'label'       => $s['label'] ?? null,
                    'order'       => $order,
                ],
            );
        }
    }
}