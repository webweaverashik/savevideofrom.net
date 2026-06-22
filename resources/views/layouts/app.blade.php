<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'SaveVideoFrom.net'))</title>
    <meta name="description" content="@yield('description', 'Free universal video downloader. YouTube, Facebook, Instagram, TikTok, X and more.')">
    <link rel="canonical" href="{{ url()->current() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function() {
            const t = localStorage.getItem('theme');
            const dark = t ? t === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>
</head>

<body class="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100 antialiased">
    <header class="border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-5xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="font-bold text-lg">SaveVideoFrom<span
                    class="text-violet-600">.net</span></a>
            <button id="themeToggle" type="button"
                class="text-sm px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-700">Theme</button>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-10">
        @yield('content')
    </main>

    <footer class="border-t border-gray-200 dark:border-gray-800 mt-16">
        <div class="max-w-5xl mx-auto px-4 py-6 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} SaveVideoFrom.net — Free universal video downloader.
        </div>
    </footer>
</body>

</html>
