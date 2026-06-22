@extends('layouts.app')

@section('content')
<section class="text-center mb-6">
    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Download Any Video, Free</h1>
    <p class="mt-3 text-gray-600 dark:text-gray-400">Paste a link from YouTube, Facebook, Instagram, TikTok, X and more.</p>
</section>

<div class="flex justify-center gap-1 mb-6">
    <button id="tabSingle" type="button" data-tab="single"
        class="px-4 py-2 rounded-lg text-sm font-medium bg-violet-600 text-white">Single Video</button>
    <button id="tabBatch" type="button" data-tab="batch"
        class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">Playlist</button>
</div>

{{-- ───────────────── Single video ───────────────── --}}
<div id="singleMode">
    <form id="dlForm" class="flex flex-col sm:flex-row gap-3">
        <input id="urlInput" type="url" inputmode="url" autocomplete="off" placeholder="Paste video URL here…"
            class="flex-1 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3 outline-none focus:ring-2 focus:ring-violet-500">
        <button id="submitBtn" type="submit"
            class="rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-semibold px-6 py-3 transition disabled:opacity-60">
            <span id="btnText">Download</span>
        </button>
    </form>

    <div id="errorBox" class="hidden mt-4 rounded-xl border border-red-300 bg-red-50 dark:bg-red-950/40 dark:border-red-900 text-red-700 dark:text-red-300 px-4 py-3 text-sm"></div>

    <div id="loadingBox" class="hidden mt-8 text-center">
        <div class="inline-block h-8 w-8 rounded-full border-2 border-violet-500 border-t-transparent animate-spin"></div>
        <p id="loadingText" class="mt-3 text-gray-500 text-sm">Fetching video…</p>
    </div>

    <div id="resultCard" class="hidden mt-8 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden">
        <div class="flex gap-4 p-4">
            <img id="thumb" alt="" class="hidden w-32 h-20 object-cover rounded-lg bg-gray-200 dark:bg-gray-800">
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
            class="inline-block rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-8 py-3 transition">Save File</a>
        <p class="mt-2 text-xs text-gray-500">Link expires after a short time.</p>
    </div>
</div>

{{-- ───────────────── Playlist / batch ───────────────── --}}
<div id="batchMode" class="hidden">
    <form id="batchForm" class="flex flex-col sm:flex-row gap-3">
        <input id="batchUrl" type="url" inputmode="url" autocomplete="off" placeholder="Paste a playlist URL…"
            class="flex-1 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3 outline-none focus:ring-2 focus:ring-violet-500">
        <button id="batchLoadBtn" type="submit"
            class="rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-semibold px-6 py-3 transition disabled:opacity-60">
            <span id="batchLoadText">Load</span>
        </button>
    </form>

    <div id="batchError" class="hidden mt-4 rounded-xl border border-red-300 bg-red-50 dark:bg-red-950/40 dark:border-red-900 text-red-700 dark:text-red-300 px-4 py-3 text-sm"></div>

    <div id="batchLoading" class="hidden mt-8 text-center">
        <div class="inline-block h-8 w-8 rounded-full border-2 border-violet-500 border-t-transparent animate-spin"></div>
        <p class="mt-3 text-gray-500 text-sm">Loading playlist…</p>
    </div>

    <div id="batchPicker" class="hidden mt-8 rounded-2xl border border-gray-200 dark:border-gray-800 p-4">
        <div class="flex items-center justify-between mb-3 gap-3">
            <h2 id="batchTitle" class="font-semibold truncate"></h2>
            <label class="text-sm flex items-center gap-2 shrink-0">
                <input id="batchSelectAll" type="checkbox" checked> Select all
            </label>
        </div>
        <p id="batchTruncated" class="hidden text-xs text-amber-600 mb-2"></p>
        <div id="batchEntries" class="max-h-80 overflow-y-auto space-y-1 pr-1"></div>

        <div class="mt-4 flex flex-col sm:flex-row gap-3">
            <select id="batchQuality"
                class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-violet-500">
                <option value="video|720p|mp4">Video · 720p (MP4)</option>
                <option value="video|1080p|mp4">Video · 1080p (MP4)</option>
                <option value="video|480p|mp4">Video · 480p (MP4)</option>
                <option value="video|360p|mp4">Video · 360p (MP4)</option>
                <option value="video|highest|mp4">Video · Best available (MP4)</option>
                <option value="audio||mp3">Audio · MP3</option>
                <option value="audio||m4a">Audio · M4A</option>
            </select>
            <button id="batchDownloadBtn" type="button"
                class="flex-1 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-semibold px-6 py-2.5 transition">
                Download selected (ZIP)
            </button>
        </div>
    </div>

    <div id="batchProgress" class="hidden mt-8 text-center">
        <p id="batchProgressText" class="text-sm text-gray-600 dark:text-gray-300 mb-3">Preparing…</p>
        <div class="h-2 w-full rounded-full bg-gray-200 dark:bg-gray-800 overflow-hidden">
            <div id="batchBar" class="h-full bg-violet-600 transition-all duration-500" style="width:0%"></div>
        </div>
        <p class="mt-2 text-xs text-gray-500">Large playlists can take a while. You can keep this tab open.</p>
    </div>

    <div id="batchDone" class="hidden mt-8 text-center">
        <a id="batchZipLink" href="#"
            class="inline-block rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-8 py-3 transition">Download ZIP</a>
        <p class="mt-2 text-xs text-gray-500">Link expires after a short time.</p>
    </div>
</div>
@endsection