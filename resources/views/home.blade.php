@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden">
        <div class="hero-glow absolute inset-x-0 -top-20 h-96 pointer-events-none" aria-hidden="true"></div>
        <div class="relative max-w-4xl mx-auto px-4 pt-16 pb-10 text-center">
            <h1 class="font-display text-4xl sm:text-5xl font-bold tracking-tight leading-tight">
                Download video from <span class="text-gradient">any site</span>
            </h1>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-400 max-w-xl mx-auto">
                Paste a link from YouTube, TikTok, Instagram, Facebook, X and 1,000+ more. Free, fast, no sign-up.
            </p>

            <div class="mt-10">@include('partials.downloader')</div>

            <div class="mt-8 flex flex-wrap justify-center gap-2">
                @foreach (['YouTube', 'Facebook', 'Instagram', 'TikTok', 'X', 'Reddit', 'Vimeo', 'Pinterest'] as $p)
                    <x-platform-pill :name="$p" />
                @endforeach
            </div>
        </div>
    </section>

    <section class="reveal-on-scroll max-w-6xl mx-auto px-4 py-12">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat value="1,000+" label="Supported sites" />
            <x-stat value="4K" label="Max video quality" />
            <x-stat value="MP3 · M4A" label="Audio formats" />
            <x-stat value="$0" label="Always free" />
        </div>
    </section>

    <section class="reveal-on-scroll max-w-6xl mx-auto px-4 py-12">
        <h2 class="font-display text-2xl font-bold text-center">Works with everything</h2>
        <p class="text-center text-gray-500 mt-2 mb-8 max-w-lg mx-auto">From social feeds to long-form video — one
            downloader for all of it.</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach ($platforms as $p)
                <x-platform-tile :name="$p->name" :color="$p->color ?? '#7c3aed'" :href="$p->is_published ? $p->landingUrl() : null" />
            @endforeach
        </div>
    </section>

    <section id="how" class="reveal-on-scroll max-w-6xl mx-auto px-4 py-16">
        <h2 class="font-display text-2xl font-bold text-center mb-10">How it works</h2>
        <div class="grid sm:grid-cols-3 gap-8">
            <x-step number="01" title="Paste the link">Copy a video URL from any supported site and drop it in the
                box.</x-step>
            <x-step number="02" title="Pick your format">Choose a resolution up to 4K, or pull just the audio as
                MP3.</x-step>
            <x-step number="03" title="Download">We process it on our servers and hand you a direct download.</x-step>
        </div>
    </section>

    @include('partials.popular-websites')
    @include('partials.supported-sites')
    @include('partials.seo-sections')

    <section class="reveal-on-scroll max-w-6xl mx-auto px-4 pb-16">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-feature-card icon="bolt" title="Fast & free">No limits, no fees, no account — just paste and
                go.</x-feature-card>
            <x-feature-card icon="globe" title="Every platform">One tool for 1,000+ sites, from social feeds to long-form
                video.</x-feature-card>
            <x-feature-card icon="sparkles" title="Up to 4K + MP3">Grab any quality, or extract the audio on its
                own.</x-feature-card>
            <x-feature-card icon="shield-check" title="Private by design">Files are deleted automatically a short time after
                download.</x-feature-card>
        </div>
    </section>

    <section id="faq" class="reveal-on-scroll max-w-4xl mx-auto px-4 pb-20">
        <h2 class="font-display text-2xl font-bold text-center mb-8">Frequently asked questions</h2>
        <x-faq-item question="Is SaveVideoFrom free?">Yes — completely free, with no limits and no
            registration.</x-faq-item>
        <x-faq-item question="Which sites are supported?">YouTube, Facebook, Instagram, TikTok, X, Reddit, Vimeo, and over a
            thousand more.</x-faq-item>
        <x-faq-item question="Can I download a whole playlist?">Yes — switch to the Playlist tab, choose the items you want,
            and download them as a single ZIP.</x-faq-item>
        <x-faq-item question="Where do my files go?">They're processed on our server, offered as a direct download, then
            deleted automatically.</x-faq-item>
        <x-faq-item question="Is it legal?">Download only content you own or have permission to use, and respect each
            platform's terms and copyright.</x-faq-item>
    </section>

    @include('partials.keywords')
@endsection
