<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Settings\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(private readonly SettingsService $settings)
    {}

    // ── Download ───────────────────────────────────────────────
    public function download(): View
    {
        return view('admin.settings.download', [
            'retention'   => $this->settings->get('retention_minutes', config('downloader.retention_minutes')),
            'maxFilesize' => $this->settings->get('max_filesize_mb', config('downloader.max_filesize_mb')),
            'maxBatch'    => $this->settings->get('max_batch_items', config('downloader.max_batch_items')),
            'cookies'     => $this->settings->get('enable_cookies', true),
        ]);
    }

    public function updateDownload(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'retention_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
            'max_filesize_mb'   => ['required', 'integer', 'min:10', 'max:10240'],
            'max_batch_items'   => ['required', 'integer', 'min:1', 'max:200'],
        ]);

        $this->settings->set('retention_minutes', $data['retention_minutes'], 'text', 'download');
        $this->settings->set('max_filesize_mb', $data['max_filesize_mb'], 'text', 'download');
        $this->settings->set('max_batch_items', $data['max_batch_items'], 'text', 'download');
        $this->settings->set('enable_cookies', $request->boolean('enable_cookies'), 'boolean', 'download');

        return back()->with('success', 'Download settings saved.');
    }

    // ── Ads ────────────────────────────────────────────────────
    public function ads(): View
    {
        return view('admin.settings.ads', ['s' => $this->settings]);
    }

    public function updateAds(Request $request): RedirectResponse
    {
        $request->validate([
            'adsense_client' => ['nullable', 'string', 'max:50'],
            'ad_header'      => ['nullable', 'string', 'max:5000'],
            'ad_footer'      => ['nullable', 'string', 'max:5000'],
            'ad_sidebar'     => ['nullable', 'string', 'max:5000'],
            'ad_in_content'  => ['nullable', 'string', 'max:5000'],
        ]);

        $this->settings->set('ads_enabled', $request->boolean('ads_enabled'), 'boolean', 'ads');
        foreach (['adsense_client', 'ad_header', 'ad_footer', 'ad_sidebar', 'ad_in_content'] as $key) {
            $this->settings->set($key, (string) $request->input($key), 'text', 'ads');
        }

        return back()->with('success', 'Ad settings saved.');
    }

    // ── SEO ────────────────────────────────────────────────────
    public function seo(): View
    {
        return view('admin.settings.seo', ['s' => $this->settings]);
    }

    public function updateSeo(Request $request): RedirectResponse
    {
        $request->validate([
            'default_meta_title'       => ['nullable', 'string', 'max:160'],
            'default_meta_description' => ['nullable', 'string', 'max:300'],
            'default_meta_keywords'    => ['nullable', 'string', 'max:300'],
            'og_image'                 => ['nullable', 'url', 'max:300'],
            'google_analytics_id'      => ['nullable', 'string', 'max:40'],
            'google_site_verification' => ['nullable', 'string', 'max:120'],
        ]);

        foreach ([
            'default_meta_title', 'default_meta_description', 'default_meta_keywords',
            'og_image', 'google_analytics_id', 'google_site_verification',
        ] as $key) {
            $this->settings->set($key, (string) $request->input($key), 'text', 'seo');
        }

        return back()->with('success', 'SEO settings saved.');
    }

    public function contact(): View
    {
        return view('admin.settings.contact', ['s' => $this->settings]);
    }

    public function updateContact(Request $request): RedirectResponse
    {
        $request->validate([
            'contact_email'   => ['nullable', 'email', 'max:150'],
            'contact_phone'   => ['nullable', 'string', 'max:50'],
            'contact_address' => ['nullable', 'string', 'max:300'],
        ]);

        foreach (['contact_email', 'contact_phone', 'contact_address'] as $key) {
            $this->settings->set($key, (string) $request->input($key), 'text', 'contact');
        }

        return back()->with('success', 'Contact info saved.');
    }
}
