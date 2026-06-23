<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Settings\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KeywordController extends Controller
{
    public function __construct(private readonly SettingsService $settings)
    {}

    public function edit(): View
    {
        $keywords = $this->settings->get('homepage_keywords', []);
        $text     = is_array($keywords) ? implode("\n", $keywords) : '';

        return view('admin.keywords.edit', compact('text'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate(['keywords' => ['nullable', 'string', 'max:5000']]);

        $list = collect(preg_split('/\r\n|\r|\n/', (string) $request->input('keywords')))
            ->map(fn($k) => trim($k))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $this->settings->set('homepage_keywords', $list, 'json', 'content');

        return back()->with('success', 'Keywords saved.');
    }
}
