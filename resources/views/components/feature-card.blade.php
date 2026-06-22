@props(['icon', 'title'])

<div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-5">
    <div class="w-11 h-11 rounded-xl bg-violet-100 dark:bg-violet-500/15 text-violet-600 dark:text-violet-400 flex items-center justify-center">
        <x-icon :name="$icon" class="w-6 h-6" />
    </div>
    <h3 class="mt-4 font-display font-semibold">{{ $title }}</h3>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $slot }}</p>
</div>