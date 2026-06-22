<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $platforms = Platform::query()->active()->published()
            ->orderByDesc('is_featured')->orderBy('sort_order')->get();

        return response()
            ->view('sitemap', compact('platforms'))
            ->header('Content-Type', 'application/xml');
    }
}
