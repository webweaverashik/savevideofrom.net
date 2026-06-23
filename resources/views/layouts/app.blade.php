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

    <header
        class="sticky top-0 z-30 backdrop-blur border-b border-gray-200/70 dark:border-white/10 bg-[#FAFAFB]/80 dark:bg-[#0B0B12]/80">
        <div class="max-w-5xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="font-display text-lg font-bold tracking-tight">
                SaveVideoFrom<span class="text-gradient">.net</span>
            </a>
            <div class="flex items-center gap-1 sm:gap-3">
                <a href="#how"
                    class="hidden sm:inline-block text-sm text-gray-600 dark:text-gray-300 hover:text-violet-600 dark:hover:text-violet-400 px-3 py-2">How
                    it works</a>
                <a href="#faq"
                    class="hidden sm:inline-block text-sm text-gray-600 dark:text-gray-300 hover:text-violet-600 dark:hover:text-violet-400 px-3 py-2">FAQ</a>
                <button id="themeToggle" type="button" aria-label="Toggle theme"
                    class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/5 transition">
                    <x-icon name="sun" class="w-5 h-5 hidden dark:block" />
                    <x-icon name="moon" class="w-5 h-5 block dark:hidden" />
                </button>
            </div>
        </div>
    </header>

    <div class="max-w-3xl mx-auto px-4"><x-ad slot="header" /></div>
    <main class="flex-1">
        @yield('content')
    </main>
    <div class="max-w-3xl mx-auto px-4"><x-ad slot="footer" /></div>

    <footer class="border-t border-gray-200/70 dark:border-white/10 mt-20">
        <div class="max-w-5xl mx-auto px-4 py-10">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="font-display font-bold">SaveVideoFrom<span class="text-gradient">.net</span></p>
                    <p class="text-sm text-gray-500 mt-1">Free universal video downloader.</p>
                </div>
                <p class="text-sm text-gray-400 dark:text-gray-500 flex flex-wrap gap-x-3 gap-y-1">
                    <span>YouTube</span><span>Facebook</span><span>Instagram</span><span>TikTok</span><span>X</span><span>Reddit</span>
                </p>
            </div>
            <div
                class="mt-8 pt-6 border-t border-gray-200/70 dark:border-white/10 text-center text-xs text-gray-400 dark:text-gray-500">
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
