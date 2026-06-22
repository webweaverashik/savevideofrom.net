@extends('layouts.app')

@section('content')
    <section class="text-center mb-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Download Any Video, Free</h1>
        <p class="mt-3 text-gray-600 dark:text-gray-400">Paste a link from YouTube, Facebook, Instagram, TikTok, X and more.
        </p>
    </section>

    <form id="dlForm" class="flex flex-col sm:flex-row gap-3">
        <input id="urlInput" type="url" inputmode="url" autocomplete="off" placeholder="Paste video URL here…"
            class="flex-1 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3 outline-none focus:ring-2 focus:ring-violet-500">
        <button id="submitBtn" type="submit"
            class="rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-semibold px-6 py-3 transition disabled:opacity-60">
            <span id="btnText">Download</span>
        </button>
    </form>

    <div class="mt-3 text-center">
        <button type="button" id="advToggle" class="text-sm text-violet-600 hover:underline">
            Downloading a private video? Add cookies ▾
        </button>
    </div>

    <div id="advPanel" class="hidden mt-3 rounded-xl border border-gray-200 dark:border-gray-800 p-4">
        <label class="block text-sm font-medium mb-1">Cookies (Netscape <code class="text-xs">cookies.txt</code>
            format)</label>
        <textarea id="cookiesText" rows="4"
            placeholder="# Netscape HTTP Cookie File&#10;.instagram.com  TRUE  /  TRUE  ..."
            class="w-full text-xs font-mono rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 outline-none focus:ring-2 focus:ring-violet-500"></textarea>
        <div class="flex items-center gap-3 mt-2">
            <input id="cookiesFile" type="file" accept=".txt" class="text-xs">
            <span class="text-xs text-gray-500">or upload a cookies.txt file</span>
        </div>
        <p class="text-xs text-gray-500 mt-2">Used only for this download, then deleted. Never stored long-term or shared.
        </p>
    </div>

    <div id="errorBox"
        class="hidden mt-4 rounded-xl border border-red-300 bg-red-50 dark:bg-red-950/40 dark:border-red-900 text-red-700 dark:text-red-300 px-4 py-3 text-sm">
    </div>

    <div id="loadingBox" class="hidden mt-8 text-center">
        <div class="inline-block h-8 w-8 rounded-full border-2 border-violet-500 border-t-transparent animate-spin"></div>
        <p id="loadingText" class="mt-3 text-gray-500 text-sm">Fetching video…</p>
    </div>

    <div id="resultCard" class="hidden mt-8 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden">
        <div class="flex gap-4 p-4">
            <img id="thumb" alt=""
                class="hidden w-32 h-20 object-cover rounded-lg bg-gray-200 dark:bg-gray-800">
            <div class="min-w-0">
                <h2 id="title" class="font-semibold truncate"></h2>
                <p id="meta" class="text-sm text-gray-500 mt-1"></p>
            </div>
        </div>
        <div class="border-t border-gray-200 dark:border-gray-800 p-4">
            <p class="text-sm font-medium mb-3">Choose a format:</p>
            <div id="formats" class="grid grid-cols-2 sm:grid-cols-3 gap-2"></div>
        </div>
    </div>

    <div id="downloadBox" class="hidden mt-8 text-center">
        <a id="downloadLink" href="#"
            class="inline-block rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-8 py-3 transition">
            Save File
        </a>
        <p class="mt-2 text-xs text-gray-500">Link expires after a short time.</p>
    </div>
@endsection
