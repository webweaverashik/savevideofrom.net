<?php

declare (strict_types = 1);

namespace Database\Seeders;

use App\Models\Page;
use App\Services\Settings\SettingsService;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            ['privacy-policy', 'Privacy Policy', '<h2>Overview</h2><p>SaveVideoFrom.net respects your privacy. This page explains what limited information we handle when you use the service.</p><h2>What we process</h2><p>We do not require an account. We do not store the videos you download — files are processed temporarily and deleted automatically a short time later. We may keep anonymised, aggregated usage statistics to operate and improve the service.</p><h2>Cookies &amp; analytics</h2><p>We may use privacy-friendly analytics to understand traffic. You can control cookies through your browser settings.</p><h2>Contact</h2><p>Questions about privacy? Reach us through our contact page.</p>'],
            ['terms-of-service', 'Terms of Service', '<h2>Acceptance</h2><p>By using SaveVideoFrom.net you agree to these terms. If you do not agree, please do not use the service.</p><h2>Acceptable use</h2><p>You may only download content you own or have permission to use. You are responsible for complying with the terms of the platforms you download from and with applicable copyright law.</p><h2>No warranty</h2><p>The service is provided “as is”, without warranties of any kind. Availability and supported sites may change at any time.</p><h2>Changes</h2><p>We may update these terms periodically. Continued use means you accept the updated terms.</p>'],
            ['contact', 'Contact Us', '<p>Have a question, suggestion, or a site you’d like supported? Send us a message and we’ll get back to you as soon as we can.</p>'],
        ];

        foreach ($pages as [$slug, $title, $body]) {
            Page::updateOrCreate(['slug' => $slug], [
                'title'      => $title,
                'body'       => $body,
                'meta_title' => "{$title} | SaveVideoFrom.net",
                'is_published' => true,
            ]);
        }

        // Default contact info.
        $settings = app(SettingsService::class);
        $settings->set('contact_email', 'support@savevideofrom.net', 'text', 'contact');
    }
}
