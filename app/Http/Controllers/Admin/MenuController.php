<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        $header = MenuItem::where('location', 'header')->whereNull('parent_id')->with('children')->orderBy('sort_order')->get();
        $footer = MenuItem::where('location', 'footer')->whereNull('parent_id')->with('children')->orderBy('sort_order')->get();

        return view('admin.menus.index', compact('header', 'footer'));
    }

    public function create(): View
    {
        return view('admin.menus.create', ['parents' => $this->parentOptions()]);
    }

    public function store(Request $request): RedirectResponse
    {
        MenuItem::create($this->validated($request));
        return redirect()->route('admin.menus.index')->with('success', 'Menu item added.');
    }

    public function edit(MenuItem $menu): View
    {
        return view('admin.menus.edit', ['menu' => $menu, 'parents' => $this->parentOptions($menu->id)]);
    }

    public function update(Request $request, MenuItem $menu): RedirectResponse
    {
        $menu->update($this->validated($request));
        return redirect()->route('admin.menus.index')->with('success', 'Menu item updated.');
    }

    public function destroy(MenuItem $menu): RedirectResponse
    {
        $menu->children()->delete(); // remove nested links with their parent
        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', 'Menu item deleted.');
    }

    private function parentOptions(?int $excludeId = null)
    {
        return MenuItem::whereNull('parent_id')
            ->when($excludeId, fn($q) => $q->whereKeyNot($excludeId))
            ->orderBy('location')->orderBy('sort_order')->get();
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'label'      => ['required', 'string', 'max:60'],
            'url'        => ['nullable', 'string', 'max:300'],
            'location'   => ['required', Rule::in(['header', 'footer'])],
            'parent_id'  => ['nullable', 'integer', 'exists:menu_items,id'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
        ]);

        // Children inherit their parent's location (keeps the tree consistent).
        if (! empty($data['parent_id']) && $parent = MenuItem::find($data['parent_id'])) {
            $data['location'] = $parent->location;
        }

        $data['is_active']    = $request->boolean('is_active');
        $data['open_new_tab'] = $request->boolean('open_new_tab');

        return $data;
    }
}
