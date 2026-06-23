<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportedSite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportedSiteController extends Controller
{
    public function index(Request $request): View
    {
        $query = SupportedSite::query()->orderBy('sort_order')->orderBy('name');

        if ($search = trim((string) $request->query('q'))) {
            $query->where('name', 'like', "%{$search}%");
        }

        $sites = $query->paginate(30)->withQueryString();

        return view('admin.sites.index', compact('sites'));
    }

    public function create(): View
    {
        return view('admin.sites.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        SupportedSite::create($data);

        return redirect()->route('admin.sites.index')->with('success', "“{$data['name']}” added.");
    }

    public function edit(SupportedSite $site): View
    {
        return view('admin.sites.edit', compact('site'));
    }

    public function update(Request $request, SupportedSite $site): RedirectResponse
    {
        $site->update($this->validated($request));

        return redirect()->route('admin.sites.index')->with('success', "“{$site->name}” updated.");
    }

    public function destroy(SupportedSite $site): RedirectResponse
    {
        $name = $site->name;
        $site->delete();

        return redirect()->route('admin.sites.index')->with('success', "“{$name}” deleted.");
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:80'],
            'url'         => ['nullable', 'url', 'max:300'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order'  => ['required', 'integer', 'min:0', 'max:9999'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
