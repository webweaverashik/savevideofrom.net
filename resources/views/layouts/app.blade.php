<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', \App\Models\SiteSetting::value('default_meta_title', config('app.name', 'SaveVideoFrom.net') . ' — Free Universal Video Downloader'))</title>
    <meta name="description" content="@yield('description', \App\Models\SiteSetting::value('default_meta_description', 'Download videos from YouTube, TikTok, Instagram, Facebook, X and 1,000+ more sites. Free, fast, no sign-up.'))">
    <meta name="keywords"
        content="{{ \App\Models\SiteSetting::value('default_meta_keywords', 'video downloader, youtube downloader, tiktok downloader') }}">
    <link rel="canonical" href="{{ url()->current() }}">

    @if ($ogImage = \App\Models\SiteSetting::value('og_image'))
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    @if ($gsv = \App\Models\SiteSetting::value('google_site_verification'))
        <meta name="google-site-verification" content="{{ $gsv }}">
    @endif
    @if ($gaId = \App\Models\SiteSetting::value('google_analytics_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '{{ $gaId }}');
        </script>
    @endif
    @if ($gtm = \App\Models\SiteSetting::value('google_tag_manager_id'))
        <script>
            (function(w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start': new Date().getTime(),
                    event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s),
                    dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', '{{ $gtm }}');
        </script>
    @endif
    @if ($bing = \App\Models\SiteSetting::value('bing_verification'))
        <meta name="msvalidate.01" content="{{ $bing }}">
    @endif
    @php
        $adsClient = \App\Models\SiteSetting::value('adsense_client');
        $adsOn = \App\Models\SiteSetting::value('ads_enabled', false);
    @endphp
    @if ($adsOn && $adsClient)
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $adsClient }}"
            crossorigin="anonymous"></script>
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        (function() {
            const t = localStorage.getItem('theme');
            const dark = t ? t === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>
</head>

<body
    class="min-h-screen flex flex-col bg-[#FAFAFB] text-gray-900 dark:bg-[#0B0B12] dark:text-gray-100 antialiased selection:bg-violet-500/30">
    @if ($gtm = \App\Models\SiteSetting::value('google_tag_manager_id'))
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtm }}" height="0"
                width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif

    <header
        class="sticky top-0 z-30 backdrop-blur border-b border-gray-200/70 dark:border-white/10 bg-[#FAFAFB]/80 dark:bg-[#0B0B12]/80">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="font-display text-lg font-bold tracking-tight shrink-0">
                SaveVideoFrom<span class="text-gradient">.net</span>
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden lg:flex items-center gap-1">
                @foreach (\App\Models\MenuItem::tree('header') as $item)
                    @if ($item->children->isNotEmpty())
                        <div class="relative nav-dropdown">
                            <button type="button" data-dropdown
                                class="flex items-center gap-1 px-3 py-2 text-sm rounded-lg text-gray-600 dark:text-gray-300 hover:text-violet-600 dark:hover:text-violet-400">
                                {{ $item->label }}<x-icon name="chevron-down" class="w-4 h-4" />
                            </button>
                            <div data-dropdown-panel
                                class="hidden absolute right-0 mt-1 w-56 rounded-xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-[#14141F] shadow-lg p-1 z-40">
                                @foreach ($item->children as $child)
                                    <a href="{{ $child->url }}"
                                        @if ($child->open_new_tab) target="_blank" rel="noopener" @endif
                                        class="block px-3 py-2 text-sm rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5">{{ $child->label }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $item->url }}"
                            @if ($item->open_new_tab) target="_blank" rel="noopener" @endif
                            class="px-3 py-2 text-sm rounded-lg text-gray-600 dark:text-gray-300 hover:text-violet-600 dark:hover:text-violet-400">{{ $item->label }}</a>
                    @endif
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                <button id="themeToggle" type="button" aria-label="Toggle theme"
                    class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/5 transition">
                    <x-icon name="sun" class="w-5 h-5 hidden dark:block" />
                    <x-icon name="moon" class="w-5 h-5 block dark:hidden" />
                </button>
                <button id="navBurger" type="button" aria-label="Menu" aria-expanded="false"
                    class="lg:hidden w-9 h-9 inline-flex items-center justify-center rounded-lg border border-gray-200 dark:border-white/10">
                    <x-icon name="list" class="w-5 h-5" />
                </button>
            </div>
        </div>

        {{-- Mobile nav --}}
        <div id="mobileNav"
            class="hidden lg:hidden border-t border-gray-200/70 dark:border-white/10 px-4 py-3 space-y-1">
            @foreach (\App\Models\MenuItem::tree('header') as $item)
                @if ($item->children->isNotEmpty())
                    <button type="button" data-mobile-sub
                        class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5">
                        {{ $item->label }}<x-icon name="chevron-down" class="w-4 h-4" />
                    </button>
                    <div class="hidden pl-3 space-y-1">
                        @foreach ($item->children as $child)
                            <a href="{{ $child->url }}"
                                @if ($child->open_new_tab) target="_blank" rel="noopener" @endif
                                class="block px-3 py-2 text-sm rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5">{{ $child->label }}</a>
                        @endforeach
                    </div>
                @else
                    <a href="{{ $item->url }}"
                        @if ($item->open_new_tab) target="_blank" rel="noopener" @endif
                        class="block px-3 py-2 text-sm rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5">{{ $item->label }}</a>
                @endif
            @endforeach
        </div>
    </header>

    <div class="max-w-5xl mx-auto w-full px-4"><x-ad slot="header" /></div>

    <main class="flex-1">
        @yield('content')
    </main>

    <div class="max-w-5xl mx-auto w-full px-4"><x-ad slot="footer" /></div>

    <footer class="border-t border-gray-200/70 dark:border-white/10 mt-20">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid gap-8 md:grid-cols-4">
                <div>
                    <p class="font-display font-bold">SaveVideoFrom<span class="text-gradient">.net</span></p>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">Free universal video downloader. Save videos,
                        audio, and images from across the web — fast, free, and no sign-up.</p>
                </div>
                @foreach (\App\Models\MenuItem::tree('footer') as $col)
                    <div>
                        <p class="font-semibold text-sm mb-3">{{ $col->label }}</p>
                        <ul class="space-y-2">
                            @foreach ($col->children as $link)
                                <li>
                                    <a href="{{ $link->url }}"
                                        @if ($link->open_new_tab) target="_blank" rel="noopener" @endif
                                        class="text-sm text-gray-500 hover:text-violet-600 dark:hover:text-violet-400">{{ $link->label }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
            <div
                class="mt-10 pt-6 border-t border-gray-200/70 dark:border-white/10 text-center text-xs text-gray-400 dark:text-gray-500">
                &copy; {{ date('Y') }} SaveVideoFrom.net. Not affiliated with any platform. Download only content
                you have the right to.
            </div>
        </div>
    </footer>

    <button id="scrollTop" type="button" aria-label="Scroll to top" class="scroll-top">
        <x-icon name="arrow-up" class="w-5 h-5" />
    </button>
</body>

</html>
