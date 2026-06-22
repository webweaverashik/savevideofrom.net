<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use App\Rules\NetscapeCookieFile;
use App\Services\Download\CookieManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CookieController extends Controller
{
    public function __construct(private readonly CookieManager $cookies)
    {}

    public function index(): View
    {
        $platforms = Platform::orderByDesc('is_active')->orderBy('sort_order')->orderBy('name')->get()
            ->each(fn(Platform $p) => $p->cookie_count = $this->cookies->count($p->slug));

        return view('admin.cookies.index', compact('platforms'));
    }

    public function show(Platform $platform): View
    {
        $files = $this->cookies->list($platform->slug);
        return view('admin.cookies.show', compact('platform', 'files'));
    }

    public function store(Request $request, Platform $platform): RedirectResponse
    {
        $request->validate([
            'cookies'   => ['required', 'array', 'max:10'],
            'cookies.*' => ['required', 'file', 'max:200', 'extensions:txt', new NetscapeCookieFile()],
        ], [
            'cookies.required'     => 'Choose at least one cookies.txt file to upload.',
            'cookies.*.extensions' => 'Cookie files must be .txt files.',
            'cookies.*.max'        => 'Each cookie file must be under 200 KB.',
        ]);

        $count = 0;
        foreach ($request->file('cookies') as $file) {
            $this->cookies->store($platform->slug, $file);
            $count++;
        }

        return redirect()
            ->route('admin.cookies.show', $platform->slug)
            ->with('success', "{$count} cookie file(s) uploaded for {$platform->name}.");
    }

    public function destroy(Platform $platform, string $file): RedirectResponse
    {
        $deleted = $this->cookies->delete($platform->slug, $file);

        return redirect()
            ->route('admin.cookies.show', $platform->slug)
            ->with($deleted ? 'success' : 'error', $deleted ? 'Cookie file deleted.' : 'Could not delete that file.');
    }
}
