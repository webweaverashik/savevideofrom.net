<div
    class="reveal text-left rounded-3xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.04] shadow-xl shadow-violet-500/5 p-4 sm:p-6">
    <div class="inline-flex p-1 rounded-xl bg-gray-100 dark:bg-white/5 mb-5">
        <button id="tabSingle" type="button" data-tab="single"
            class="px-4 py-2 rounded-lg text-sm font-medium transition bg-violet-600 text-white">Single Video</button>
        <button id="tabBatch" type="button" data-tab="batch"
            class="px-4 py-2 rounded-lg text-sm font-medium transition text-gray-600 dark:text-gray-300">Playlist</button>
    </div>

    {{-- Single --}}
    <div id="singleMode">
        <form id="dlForm" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 pointer-events-none">
                    <x-icon name="link" class="w-5 h-5" />
                </span>
                <input id="urlInput" type="url" inputmode="url" autocomplete="off"
                    placeholder="Paste video URL here…"
                    class="w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 pl-10 pr-4 py-3 outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <button id="submitBtn" type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-semibold px-6 py-3 transition disabled:opacity-60">
                <x-icon name="download" class="w-5 h-5" /><span id="btnText">Download</span>
            </button>
        </form>

        <div id="errorBox"
            class="hidden mt-4 rounded-xl border border-red-300 bg-red-50 dark:bg-red-950/40 dark:border-red-900 text-red-700 dark:text-red-300 px-4 py-3 text-sm">
        </div>

        <div id="loadingBox" class="hidden mt-6">
            <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 p-4">
                <div class="flex gap-4">
                    <div
                        class="scan-beam w-32 h-20 rounded-lg bg-gray-200 dark:bg-white/10 flex items-center justify-center text-violet-500/70">
                        <x-icon name="play" class="w-7 h-7" />
                    </div>
                    <div class="flex-1 space-y-2 py-1">
                        <div class="h-4 w-3/4 rounded bg-gray-200 dark:bg-white/10 animate-pulse"></div>
                        <div class="h-3 w-1/2 rounded bg-gray-200 dark:bg-white/10 animate-pulse"></div>
                        <div class="mt-3 h-1.5 w-full rounded-full bg-gray-200/70 dark:bg-white/10 bar-indeterminate">
                        </div>
                    </div>
                </div>
            </div>
            <p class="mt-3 flex items-center justify-center gap-2 text-gray-500 text-sm">
                <span class="inline-flex gap-1" aria-hidden="true">
                    <span class="dot w-1.5 h-1.5 rounded-full bg-violet-500"></span>
                    <span class="dot w-1.5 h-1.5 rounded-full bg-violet-500"></span>
                    <span class="dot w-1.5 h-1.5 rounded-full bg-violet-500"></span>
                </span>
                <span id="loadingText">Fetching video…</span>
            </p>
        </div>

        <div id="resultCard"
            class="reveal hidden mt-6 rounded-2xl border border-gray-200/70 dark:border-white/10 overflow-hidden">
            <div class="flex gap-4 p-4">
                <img id="thumb" alt=""
                    class="hidden w-32 h-20 object-cover rounded-lg bg-gray-200 dark:bg-gray-800">
                <div class="min-w-0">
                    <h2 id="title" class="font-semibold truncate"></h2>
                    <p id="meta" class="text-sm text-gray-500 mt-1"></p>
                </div>
            </div>
            <div class="border-t border-gray-200/70 dark:border-white/10 p-4">
                <p class="text-sm font-medium mb-3">Choose a format</p>
                <div id="formats" class="grid grid-cols-2 sm:grid-cols-3 gap-2"></div>
            </div>
        </div>

        <div id="downloadBox" class="reveal hidden mt-6 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Your download should start automatically.</p>
            <a id="downloadLink" href="#"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-8 py-3 transition">
                <x-icon name="download" class="w-5 h-5" />Didn't start? Save file
            </a>
            <p class="mt-2 text-xs text-gray-500">Link expires after a short time.</p>
        </div>
    </div>

    {{-- Playlist --}}
    <div id="batchMode" class="hidden">
        <form id="batchForm" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 pointer-events-none">
                    <x-icon name="link" class="w-5 h-5" />
                </span>
                <input id="batchUrl" type="url" inputmode="url" autocomplete="off"
                    placeholder="Paste a playlist URL…"
                    class="w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 pl-10 pr-4 py-3 outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <button id="batchLoadBtn" type="submit"
                class="rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-semibold px-6 py-3 transition disabled:opacity-60">
                <span id="batchLoadText">Load</span>
            </button>
        </form>

        <div id="batchError"
            class="hidden mt-4 rounded-xl border border-red-300 bg-red-50 dark:bg-red-950/40 dark:border-red-900 text-red-700 dark:text-red-300 px-4 py-3 text-sm">
        </div>

        <div id="batchLoading" class="hidden mt-6 space-y-2 animate-pulse">
            <div class="h-12 rounded-lg bg-gray-200 dark:bg-white/10"></div>
            <div class="h-12 rounded-lg bg-gray-200 dark:bg-white/10"></div>
            <div class="h-12 rounded-lg bg-gray-200 dark:bg-white/10"></div>
        </div>

        <div id="batchPicker" class="reveal hidden mt-6 rounded-2xl border border-gray-200/70 dark:border-white/10 p-4">
            <div class="flex items-center justify-between mb-3 gap-3">
                <h2 id="batchTitle" class="font-semibold truncate"></h2>
                <label class="text-sm flex items-center gap-2 shrink-0"><input id="batchSelectAll" type="checkbox"
                        checked> Select all</label>
            </div>
            <p id="batchTruncated" class="hidden text-xs text-amber-600 mb-2"></p>
            <div id="batchEntries" class="max-h-80 overflow-y-auto space-y-1 pr-1"></div>
            <div class="mt-4 flex flex-col sm:flex-row gap-3">
                <select id="batchQuality"
                    class="rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-violet-500">
                    <option value="video|720p|mp4">Video · 720p (MP4)</option>
                    <option value="video|1080p|mp4">Video · 1080p (MP4)</option>
                    <option value="video|480p|mp4">Video · 480p (MP4)</option>
                    <option value="video|360p|mp4">Video · 360p (MP4)</option>
                    <option value="video|highest|mp4">Video · Best available (MP4)</option>
                    <option value="audio||mp3">Audio · MP3</option>
                    <option value="audio||m4a">Audio · M4A</option>
                </select>
                <button id="batchDownloadBtn" type="button"
                    class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-semibold px-6 py-2.5 transition">
                    <x-icon name="download" class="w-5 h-5" />Download selected (ZIP)
                </button>
            </div>
        </div>

        <div id="batchProgress" class="hidden mt-6 text-center">
            <p id="batchProgressText" class="text-sm text-gray-600 dark:text-gray-300 mb-3">Preparing…</p>
            <div class="h-2 w-full rounded-full bg-gray-200 dark:bg-white/10 overflow-hidden">
                <div id="batchBar"
                    class="h-full bg-gradient-to-r from-violet-600 to-fuchsia-600 transition-all duration-500"
                    style="width:0%"></div>
            </div>
            <p class="mt-2 text-xs text-gray-500">Large playlists take a while. You can keep this tab open.</p>
        </div>

        <div id="batchDone" class="reveal hidden mt-6 text-center">
            <a id="batchZipLink" href="#"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-8 py-3 transition">
                <x-icon name="download" class="w-5 h-5" />Download ZIP
            </a>
            <p class="mt-2 text-xs text-gray-500">Link expires after a short time.</p>
        </div>
    </div>
</div>
