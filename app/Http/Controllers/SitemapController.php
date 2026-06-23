<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Platform;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $platforms = Platform::query()->active()->published()
            ->orderByDesc('is_featured')->orderBy('sort_order')->get();

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $xml .= $this->url(url('/'), 'daily', '1.0');

        foreach ($platforms as $p) {
            $xml .= $this->url(route('landing', $p->page_slug), 'weekly', '0.8');
        }

        foreach (Page::where('is_published', true)->get() as $page) {
            $loc = match ($page->slug) {
                'privacy-policy'   => route('page.privacy'),
                'terms-of-service' => route('page.terms'),
                'contact'          => route('contact'),
                default            => null,
            };
            if ($loc) {
                $xml .= $this->url($loc, 'monthly', '0.5');
            }
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    private function url(string $loc, string $freq, string $priority): string
    {
        return '  <url><loc>' . htmlspecialchars($loc, ENT_XML1) . "</loc>"
            . "<changefreq>{$freq}</changefreq><priority>{$priority}</priority></url>\n";
    }
}
