@extends('layouts.app')

@section('content')
    {{-- ───────────────── Hero: the live downloader ───────────────── --}}
    <section class="relative overflow-hidden">
        <div class="hero-glow absolute inset-x-0 -top-20 h-96 pointer-events-none" aria-hidden="true"></div>

        <div class="relative max-w-3xl mx-auto px-4 pt-16 pb-10 text-center">
            <h1 class="font-display text-4xl sm:text-5xl font-bold tracking-tight leading-tight">
                Download video from <span class="text-gradient">any site</span>
            </h1>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-400 max-w-xl mx-auto">
                Paste a link from YouTube, TikTok, Instagram, Facebook, X and 1,000+ more. Free, fast, no sign-up.
            </p>

            {{-- Downloader card --}}
            <div
                class="reveal mt-10 text-left rounded-3xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.04] shadow-xl shadow-violet-500/5 p-4 sm:p-6">
                <div class="inline-flex p-1 rounded-xl bg-gray-100 dark:bg-white/5 mb-5">
                    <button id="tabSingle" type="button" data-tab="single"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition bg-violet-600 text-white">Single
                        Video</button>
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
                                    <div
                                        class="mt-3 h-1.5 w-full rounded-full bg-gray-200/70 dark:bg-white/10 bar-indeterminate">
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
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Your download should start automatically.
                        </p>
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

                    <div id="batchPicker"
                        class="reveal hidden mt-6 rounded-2xl border border-gray-200/70 dark:border-white/10 p-4">
                        <div class="flex items-center justify-between mb-3 gap-3">
                            <h2 id="batchTitle" class="font-semibold truncate"></h2>
                            <label class="text-sm flex items-center gap-2 shrink-0"><input id="batchSelectAll"
                                    type="checkbox" checked> Select all</label>
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

            {{-- Works-with strip --}}
            <div class="mt-8 flex flex-wrap justify-center gap-2">
                @foreach (['YouTube', 'Facebook', 'Instagram', 'TikTok', 'X', 'Reddit', 'Vimeo', 'Pinterest'] as $p)
                    <x-platform-pill :name="$p" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- ───────────────── Stats strip ───────────────── --}}
    <section class="reveal-on-scroll max-w-4xl mx-auto px-4 py-12">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat value="1,000+" label="Supported sites" />
            <x-stat value="4K" label="Max video quality" />
            <x-stat value="MP3 · M4A" label="Audio formats" />
            <x-stat value="$0" label="Always free" />
        </div>
    </section>

    {{-- ───────────────── Platforms grid ───────────────── --}}
    <section class="reveal-on-scroll max-w-4xl mx-auto px-4 py-12">
        <h2 class="font-display text-2xl font-bold text-center">Works with everything</h2>
        <p class="text-center text-gray-500 mt-2 mb-8 max-w-lg mx-auto">From social feeds to long-form video — one
            downloader for all of it.</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @php
                $platforms = [
                    ['YouTube', '#FF0000'],
                    ['Facebook', '#1877F2'],
                    ['Instagram', '#E1306C'],
                    ['TikTok', '#EE1D52'],
                    ['X (Twitter)', '#1D9BF0'],
                    ['Reddit', '#FF4500'],
                    ['Vimeo', '#1AB7EA'],
                    ['Pinterest', '#E60023'],
                    ['LinkedIn', '#0A66C2'],
                    ['1,000+ more', '#7C3AED'],
                ];
            @endphp
            @foreach ($platforms as [$name, $color])
                <x-platform-tile :name="$name" :color="$color" />
            @endforeach
        </div>
    </section>

    {{-- ───────────────── How it works ───────────────── --}}
    <section id="how" class="reveal-on-scroll max-w-4xl mx-auto px-4 py-16">
        <h2 class="font-display text-2xl font-bold text-center mb-10">How it works</h2>
        <div class="grid sm:grid-cols-3 gap-8">
            <x-step number="01" title="Paste the link">Copy a video URL from any supported site and drop it in the
                box.</x-step>
            <x-step number="02" title="Pick your format">Choose a resolution up to 4K, or pull just the audio as
                MP3.</x-step>
            <x-step number="03" title="Download">We process it on our servers and hand you a direct download.</x-step>
        </div>
    </section>

    {{-- ───────────────── Features ───────────────── --}}
    <section class="reveal-on-scroll max-w-4xl mx-auto px-4 pb-16">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-feature-card icon="bolt" title="Fast & free">No limits, no fees, no account — just paste and
                go.</x-feature-card>
            <x-feature-card icon="globe" title="Every platform">One tool for 1,000+ sites, from social feeds to
                long-form video.</x-feature-card>
            <x-feature-card icon="sparkles" title="Up to 4K + MP3">Grab any quality, or extract the audio on its
                own.</x-feature-card>
            <x-feature-card icon="shield-check" title="Private by design">Files are deleted automatically a short time
                after download.</x-feature-card>
        </div>
    </section>

    {{-- ───────────────── Quality & format infographic ───────────────── --}}
    <section class="reveal-on-scroll max-w-4xl mx-auto px-4 pb-16">
        <div class="grid md:grid-cols-2 gap-4">
            <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-6">
                <div
                    class="w-11 h-11 rounded-xl bg-violet-100 dark:bg-violet-500/15 text-violet-600 dark:text-violet-400 flex items-center justify-center">
                    <x-icon name="play" class="w-6 h-6" />
                </div>
                <h3 class="mt-4 font-display font-semibold">Any video quality</h3>
                <p class="text-sm text-gray-500 mt-1">From 144p all the way to crisp 4K — you choose.</p>
                <div class="mt-4 flex flex-wrap items-end gap-2">
                    @php $qs = ['144p','240p','360p','480p','720p','1080p','4K']; @endphp
                    @foreach ($qs as $i => $q)
                        <span
                            class="rounded-lg bg-violet-100 dark:bg-violet-500/15 text-violet-700 dark:text-violet-300 font-semibold px-2.5 py-1"
                            style="font-size: {{ 0.72 + $i * 0.08 }}rem;">{{ $q }}</span>
                    @endforeach
                </div>
            </div>
            <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-6">
                <div
                    class="w-11 h-11 rounded-xl bg-fuchsia-100 dark:bg-fuchsia-500/15 text-fuchsia-600 dark:text-fuchsia-400 flex items-center justify-center">
                    <x-icon name="sparkles" class="w-6 h-6" />
                </div>
                <h3 class="mt-4 font-display font-semibold">Audio only</h3>
                <p class="text-sm text-gray-500 mt-1">Skip the video and pull just the sound, in your preferred format.</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach (['MP3', 'M4A', 'WAV', 'AAC'] as $a)
                        <span
                            class="rounded-lg border border-gray-200 dark:border-white/10 px-3 py-1.5 text-sm font-medium">{{ $a }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ───────────────── FAQ ───────────────── --}}
    <section id="faq" class="reveal-on-scroll max-w-3xl mx-auto px-4 pb-20">
        <h2 class="font-display text-2xl font-bold text-center mb-8">Frequently asked questions</h2>
        <x-faq-item question="Is SaveVideoFrom free?">Yes — completely free, with no limits and no
            registration.</x-faq-item>
        <x-faq-item question="Which sites are supported?">YouTube, Facebook, Instagram, TikTok, X, Reddit, Vimeo, and over
            a thousand more.</x-faq-item>
        <x-faq-item question="Can I download a whole playlist?">Yes — switch to the Playlist tab, choose the items you
            want, and download them as a single ZIP.</x-faq-item>
        <x-faq-item question="Where do my files go?">They're processed on our server, offered as a direct download, then
            deleted automatically.</x-faq-item>
        <x-faq-item question="Is it legal?">Download only content you own or have permission to use, and respect each
            platform's terms and copyright.</x-faq-item>
    </section>
@endsection
