@props(['platform'])

@php
    $name = $platform->name . ' Video Downloader';
    $faqs = is_array($platform->faqs) ? $platform->faqs : [];
    $howto = is_array($platform->howto) ? $platform->howto : [];

    $graph = [
        [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => $name,
            'applicationCategory' => 'UtilitiesApplication',
            'operatingSystem' => 'Any',
            'url' => url()->current(),
            'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'USD'],
        ],
        [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $name, 'item' => url()->current()],
            ],
        ],
    ];

    if ($faqs) {
        $graph[] = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(
                fn($f) => [
                    '@type' => 'Question',
                    'name' => $f['q'] ?? '',
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a'] ?? ''],
                ],
                $faqs,
            ),
        ];
    }

    if ($howto) {
        $graph[] = [
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => 'How to download from ' . $platform->name,
            'step' => array_map(
                fn($i, $s) => [
                    '@type' => 'HowToStep',
                    'position' => $i + 1,
                    'name' => $s['title'] ?? '',
                    'text' => $s['text'] ?? '',
                ],
                array_keys($howto),
                $howto,
            ),
        ];
    }
@endphp

@foreach ($graph as $block)
    <script type="application/ld+json">{!! json_encode($block, JSON_UNESCAPED_UNICODE) !!}</script>
@endforeach
