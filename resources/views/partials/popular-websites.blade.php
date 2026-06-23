@php
    $popular = \App\Models\Platform::query()
        ->active()
        ->whereNotNull('card_description')
        ->orderByDesc('is_featured')
        ->orderBy('sort_order')
        ->get();
@endphp

@if ($popular->isNotEmpty())
    <section class="reveal-on-scroll max-w-5xl mx-auto px-4 py-16">
        <div class="text-center mb-10">
            <h2 class="font-display text-2xl sm:text-3xl font-bold">Online Video Downloaders for Popular Websites</h2>
            <p class="text-gray-500 mt-2">Download in HD, 4K, MP4 video &amp; MP3 audio.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {{-- All-in-one card --}}
            <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-5">
                <h3 class="font-display font-semibold">
                    <a href="{{ route('home') }}" class="hover:text-violet-600 dark:hover:text-violet-400">All Video
                        Downloader</a>
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    Paste a link from almost any site and save the video in HD — no installs, no sign-up, right from
                    your browser.
                </p>
            </div>
            @foreach ($popular as $p)
                @php $url = $p->is_published ? $p->landingUrl() : null; @endphp
                <div
                    class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-5">
                    <h3 class="font-display font-semibold">
                        @if ($url)
                            <a href="{{ $url }}"
                                class="hover:text-violet-600 dark:hover:text-violet-400">{{ $p->name }} Video
                                Downloader</a>
                        @else
                            {{ $p->name }} Video Downloader
                        @endif
                    </h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $p->card_description }}
                    </p>
                </div>
            @endforeach
        </div>
    </section>
@endif
