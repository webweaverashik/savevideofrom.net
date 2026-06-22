@props(['items' => []])

<nav aria-label="Breadcrumb" class="text-sm">
    <ol class="flex flex-wrap items-center gap-1.5 text-gray-500">
        @foreach ($items as [$label, $url])
            <li class="flex items-center gap-1.5">
                @if ($url && ! $loop->last)
                    <a href="{{ $url }}" class="hover:text-violet-600 dark:hover:text-violet-400">{{ $label }}</a>
                    <span class="text-gray-300 dark:text-gray-600">/</span>
                @else
                    <span class="text-gray-700 dark:text-gray-300">{{ $label }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>