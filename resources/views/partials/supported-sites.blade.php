@php
    $sites = \App\Models\SupportedSite::query()->active()->orderBy('sort_order')->orderBy('name')->get();
@endphp

@if ($sites->isNotEmpty())
    <section class="reveal-on-scroll max-w-7xl mx-auto px-4 py-16">
        <div class="text-center mb-10">
            <h2 class="font-display text-2xl sm:text-3xl font-bold">Supports 15,000+ Popular Websites</h2>
            <p class="text-gray-500 mt-2">Download videos from thousands of sites across the web.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach ($sites as $s)
                <div class="rounded-xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-4">
                    <h3 class="text-sm font-semibold">{{ $s->name }} Video Downloader</h3>
                    <p class="mt-1 text-xs text-gray-500 leading-relaxed">{{ $s->description }}</p>
                </div>
            @endforeach
        </div>
    </section>
@endif
