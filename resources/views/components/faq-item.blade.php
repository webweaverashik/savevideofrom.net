@props(['question'])

<details class="group border-b border-gray-200/70 dark:border-white/10 py-4">
    <summary class="flex items-center justify-between cursor-pointer list-none font-medium">
        <span>{{ $question }}</span>
        <x-icon name="chevron-down" class="w-5 h-5 shrink-0 text-gray-400 transition-transform duration-200 group-open:rotate-180" />
    </summary>
    <div class="mt-3 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $slot }}</div>
</details>