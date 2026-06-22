@props(['number', 'title'])

<div>
    <div class="font-display text-sm font-bold text-violet-600 dark:text-violet-400">{{ $number }}</div>
    <div class="mt-2 h-px w-8 bg-violet-500/40"></div>
    <h3 class="mt-3 font-display font-semibold">{{ $title }}</h3>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $slot }}</p>
</div>