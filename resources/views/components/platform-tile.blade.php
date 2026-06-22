@props(['name', 'color' => '#7c3aed', 'href' => null])

@php $tag = $href ? 'a' : 'div'; @endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif
    class="flex items-center gap-3 rounded-xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] px-4 py-3 hover:border-violet-500/50 transition">
    <span class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0" style="background-color: {{ $color }}1a; color: {{ $color }};">
        <x-icon name="play" class="w-5 h-5" />
    </span>
    <span class="text-sm font-medium truncate">{{ $name }}</span>
</{{ $tag }}>