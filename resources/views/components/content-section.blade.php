@props(['image' => null, 'title', 'reverse' => false])

<section class="reveal-on-scroll max-w-6xl mx-auto px-4 py-12">
    <div class="grid md:grid-cols-2 gap-8 lg:gap-12 items-center">
        @if ($image)
            <div class="{{ $reverse ? 'md:order-2' : '' }}">
                <img src="{{ $image }}" alt="{{ $title }}" loading="lazy"
                    class="w-full rounded-2xl border border-gray-200/70 dark:border-white/10 shadow-lg shadow-violet-500/5">
            </div>
        @endif
        <div class="{{ $reverse ? 'md:order-1' : '' }}">
            <h2 class="font-display text-2xl sm:text-3xl font-bold">{{ $title }}</h2>
            <div class="mt-4 text-gray-600 dark:text-gray-400 leading-relaxed space-y-3">{{ $slot }}</div>
        </div>
    </div>
</section>
