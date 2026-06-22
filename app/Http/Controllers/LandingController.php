<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function show(string $page): View
    {
        $platform = Platform::query()
            ->active()->published()
            ->where('page_slug', $page)
            ->firstOrFail();

        $related = Platform::query()
            ->active()->published()
            ->whereKeyNot($platform->id)
            ->orderByDesc('is_featured')->orderBy('sort_order')
            ->limit(8)->get();

        return view('landing', compact('platform', 'related'));
    }
}
