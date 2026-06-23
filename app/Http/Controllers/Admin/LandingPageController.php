<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function index(): View
    {
        $platforms = Platform::orderByDesc('is_featured')->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.landing.index', compact('platforms'));
    }

    public function edit(Platform $platform): View
    {
        return view('admin.landing.edit', compact('platform'));
    }

    public function update(Request $request, Platform $platform): RedirectResponse
    {
        $data = $request->validate([
            // Must stay routable: lowercase/digits/hyphens, ending in "-downloader".
            'page_slug'        => ['required', 'string', 'max:120', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*-downloader$/', Rule::unique('platforms', 'page_slug')->ignore($platform->id)],
            'meta_title'       => ['nullable', 'string', 'max:160'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'h1'               => ['nullable', 'string', 'max:120'],
            'intro'            => ['nullable', 'string', 'max:1000'],
            'color'            => ['nullable', 'string', 'max:9'],
            'sort_order'       => ['required', 'integer', 'min:0', 'max:999'],
            'howto'            => ['nullable', 'array'],
            'howto.*.title'    => ['nullable', 'string', 'max:120'],
            'howto.*.text'     => ['nullable', 'string', 'max:500'],
            'faqs'             => ['nullable', 'array'],
            'faqs.*.q'         => ['nullable', 'string', 'max:200'],
            'faqs.*.a'         => ['nullable', 'string', 'max:1000'],
        ], [
            'page_slug.regex' => 'The slug must be lowercase words separated by hyphens and end in "-downloader" (e.g. youtube-video-downloader).',
        ]);

        $howto = collect($request->input('howto', []))
            ->map(fn ($r) => ['title' => trim((string) ($r['title'] ?? '')), 'text' => trim((string) ($r['text'] ?? ''))])
            ->filter(fn ($r) => $r['title'] !== '' || $r['text'] !== '')
            ->values()->all();

        $faqs = collect($request->input('faqs', []))
            ->map(fn ($r) => ['q' => trim((string) ($r['q'] ?? '')), 'a' => trim((string) ($r['a'] ?? ''))])
            ->filter(fn ($r) => $r['q'] !== '' || $r['a'] !== '')
            ->values()->all();

        $platform->update([
            'page_slug'        => $data['page_slug'],
            'meta_title'       => $data['meta_title'],
            'meta_description' => $data['meta_description'],
            'h1'               => $data['h1'],
            'intro'            => $data['intro'],
            'color'            => $data['color'] ?: null,
            'sort_order'       => $data['sort_order'],
            'is_active'        => $request->boolean('is_active'),
            'is_published'     => $request->boolean('is_published'),
            'is_featured'      => $request->boolean('is_featured'),
            'howto'            => $howto,
            'faqs'             => $faqs,
        ]);

        return redirect()
            ->route('admin.landing.edit', $platform)
            ->with('success', "{$platform->name} landing page saved.");
    }
}