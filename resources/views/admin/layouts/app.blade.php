<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · SaveVideoFrom.net</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css'])
    <script>
        (function() {
            const t = localStorage.getItem('admin-theme');
            document.documentElement.classList.toggle('dark', t ? t === 'dark' : true);
        })();
    </script>
</head>

<body class="min-h-screen bg-[#FAFAFB] text-gray-900 dark:bg-[#0B0B12] dark:text-gray-100 antialiased">

    @php
        $nav = [
            'Overview' => [['admin.dashboard', 'Dashboard', 'chart', false]],
            'Downloads' => [
                ['admin.logs', 'Download Logs', 'list', true],
                ['admin.cookies.index', 'Cookies', 'key', true],
            ],
            'Content' => [['admin.landing.index', 'Landing Pages', 'globe', true]],
            'Settings' => [
                ['admin.settings.download', 'Download', 'sliders', true],
                ['admin.settings.ads', 'Ads & AdSense', 'megaphone', true],
                ['admin.settings.seo', 'SEO', 'search', true],
            ],
        ];
    @endphp

    <input type="checkbox" id="navToggle" class="peer hidden">

    {{-- Mobile top bar --}}
    <div
        class="lg:hidden sticky top-0 z-40 flex items-center justify-between h-14 px-4 border-b border-gray-200/70 dark:border-white/10 bg-white/80 dark:bg-[#0B0B12]/80 backdrop-blur">
        <a href="{{ route('admin.dashboard') }}" class="font-display font-bold">SaveVideoFrom<span
                class="text-gradient">.net</span></a>
        <label for="navToggle" class="cursor-pointer p-2 rounded-lg border border-gray-200 dark:border-white/10">
            <x-icon name="list" class="w-5 h-5" />
        </label>
    </div>

    {{-- Sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full peer-checked:translate-x-0 lg:translate-x-0 transition-transform
    border-r border-gray-200/70 dark:border-white/10 bg-white dark:bg-[#0E0E18] flex flex-col">
        <div class="h-16 flex items-center px-6 border-b border-gray-200/70 dark:border-white/10">
            <a href="{{ route('admin.dashboard') }}" class="font-display text-lg font-bold">SaveVideoFrom<span
                    class="text-gradient">.net</span></a>
        </div>
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
            @foreach ($nav as $group => $items)
                <div>
                    <p class="px-3 mb-1 text-[11px] font-semibold uppercase tracking-wider text-gray-400">
                        {{ $group }}</p>
                    @foreach ($items as [$route, $label, $icon, $wildcard])
                        @php $active = $wildcard ? request()->routeIs($route . '*') : request()->routeIs($route); @endphp
                        <a href="{{ route($route) }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                        {{ $active ? 'bg-violet-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5' }}">
                            <x-icon :name="$icon" class="w-5 h-5 shrink-0" />{{ $label }}
                        </a>
                    @endforeach
                </div>
            @endforeach
        </nav>
        <div class="p-3 border-t border-gray-200/70 dark:border-white/10 flex items-center gap-2">
            <button type="button"
                onclick="const d=document.documentElement.classList.toggle('dark');localStorage.setItem('admin-theme',d?'dark':'light')"
                class="flex-1 text-sm px-3 py-2 rounded-lg border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/5">Theme</button>
            <form method="POST" action="{{ route('admin.logout') }}" class="flex-1">@csrf
                <button
                    class="w-full text-sm px-3 py-2 rounded-lg border border-gray-200 dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/5">Log
                    out</button>
            </form>
        </div>
    </aside>

    {{-- Overlay for mobile --}}
    <label for="navToggle" class="fixed inset-0 z-30 bg-black/40 hidden peer-checked:block lg:hidden"></label>

    <div class="lg:pl-64">
        <main class="px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="font-display text-2xl font-bold mb-6">@yield('title', 'Dashboard')</h1>

            @if (session('success'))
                <div
                    class="mb-5 rounded-xl border border-emerald-300 bg-emerald-50 dark:bg-emerald-950/30 dark:border-emerald-900 text-emerald-700 dark:text-emerald-300 px-4 py-3 text-sm">
                    {{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div
                    class="mb-5 rounded-xl border border-red-300 bg-red-50 dark:bg-red-950/30 dark:border-red-900 text-red-700 dark:text-red-300 px-4 py-3 text-sm">
                    {{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</body>

</html>
