@props(['value', 'label'])

<div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-5 text-center">
    <div class="font-display text-3xl font-bold text-gradient">{{ $value }}</div>
    <div class="mt-1 text-sm text-gray-500">{{ $label }}</div>
</div>