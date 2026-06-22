@extends('layouts.app')

@section('title', ($platform->meta_title ?: $platform->name . ' Video Downloader') . ' | SaveVideoFrom.net')
@section('description', $platform->meta_description ?: 'Download ' . $platform->name . ' videos free in HD. Fast, no
    sign-up.')

@section('content')
    <x-landing-schema :platform="$platform" />

    <section class="relative overflow-hidden">
        <div class="hero-glow absolute inset-x-0 -top-20 h-96 pointer-events-none" aria-hidden="true"></div>
        <div class="relative max-w-3xl mx-auto px-4 pt-8 pb-10">
            <x-breadcrumbs :items="[['Home', route('home')], [$platform->name . ' Downloader', null]]" />

            <div class="text-center mt-6">
                <h1 class="font-display text-4xl sm:text-5xl font-bold tracking-tight leading-tight">
                    {{ $platform->h1 ?: $platform->name . ' Video Downloader' }}
                </h1>
                @if ($platform->intro)
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-400 max-w-xl mx-auto">{{ $platform->intro }}</p>
                @endif
            </div>

            <div class="mt-10">@include('partials.downloader')</div>
        </div>
    </section>

    <div class="max-w-3xl mx-auto px-4"><x-ad slot="in_content" /></div>

    @if (is_array($platform->howto) && count($platform->howto))
        <section class="reveal-on-scroll max-w-4xl mx-auto px-4 py-16">
            <h2 class="font-display text-2xl font-bold text-center mb-10">How to download from {{ $platform->name }}</h2>
            <div class="grid sm:grid-cols-3 gap-8">
                @foreach ($platform->howto as $i => $step)
                    <x-step :number="sprintf('%02d', $i + 1)" :title="$step['title'] ?? ''">{{ $step['text'] ?? '' }}</x-step>
                @endforeach
            </div>
        </section>
    @endif

    <section class="reveal-on-scroll max-w-4xl mx-auto px-4 pb-16">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-feature-card icon="bolt" title="Fast & free">No limits, no fees, no account.</x-feature-card>
            <x-feature-card icon="sparkles" title="Up to 4K + MP3">Any quality, or audio on its own.</x-feature-card>
            <x-feature-card icon="globe" title="Any device">Works in the browser on phone, tablet, and
                desktop.</x-feature-card>
            <x-feature-card icon="shield-check" title="Private by design">Files auto-delete shortly after
                download.</x-feature-card>
        </div>
    </section>

    @if (is_array($platform->faqs) && count($platform->faqs))
        <section class="reveal-on-scroll max-w-3xl mx-auto px-4 pb-16">
            <h2 class="font-display text-2xl font-bold text-center mb-8">{{ $platform->name }} downloader FAQ</h2>
            @foreach ($platform->faqs as $faq)
                <x-faq-item :question="$faq['q'] ?? ''">{{ $faq['a'] ?? '' }}</x-faq-item>
            @endforeach
        </section>
    @endif

    @if ($related->isNotEmpty())
        <section class="reveal-on-scroll max-w-4xl mx-auto px-4 pb-20">
            <h2 class="font-display text-2xl font-bold text-center mb-8">More downloaders</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach ($related as $r)
                    <x-platform-tile :name="$r->name . ' Downloader'" :color="$r->color ?? '#7c3aed'" :href="$r->landingUrl()" />
                @endforeach
            </div>
        </section>
    @endif
@endsection
