<div class="flex items-center justify-between gap-3 py-2 {{ $child ? 'pl-6' : '' }}">
    <div class="min-w-0 flex items-center gap-2">
        @if ($child)
            <span class="text-gray-300 dark:text-gray-600">↳</span>
        @endif
        <span class="text-sm font-medium truncate">{{ $item->label }}</span>
        <span class="font-mono text-xs text-gray-400 truncate">{{ $item->url ?: '(heading)' }}</span>
        @unless ($item->is_active)
            <span class="text-xs text-gray-400">· hidden</span>
        @endunless
    </div>
    <div class="flex items-center gap-2 shrink-0 text-sm">
        <a href="{{ route('admin.menus.edit', $item) }}"
            class="text-violet-600 dark:text-violet-400 hover:underline">Edit</a>
        <form method="POST" action="{{ route('admin.menus.destroy', $item) }}" class="inline"
            onsubmit="return confirm('Delete “{{ $item->label }}”{{ $item->children->count() ? ' and its sub-items' : '' }}?')">
            @csrf @method('DELETE')
            <button class="text-red-600 hover:underline">Delete</button>
        </form>
    </div>
</div>
