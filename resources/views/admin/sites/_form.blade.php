@php $field = 'w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 px-3 py-2.5 outline-none focus:ring-2 focus:ring-violet-500'; @endphp

@if ($errors->any())
    <div
        class="mb-4 rounded-xl border border-red-300 bg-red-50 dark:bg-red-950/30 dark:border-red-900 text-red-700 dark:text-red-300 px-4 py-3 text-sm">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-6 space-y-5">
    <div>
        <label class="block text-sm font-medium mb-1">Site name</label>
        <input type="text" name="name" value="{{ old('name', $site->name ?? '') }}" placeholder="e.g. Twitch"
            class="{{ $field }}">
        <p class="text-xs text-gray-500 mt-1">Shown as “{name} Video Downloader” on the grid.</p>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">URL <span class="text-gray-400">(optional)</span></label>
        <input type="url" name="url" value="{{ old('url', $site->url ?? '') }}" placeholder="https://…"
            class="{{ $field }}">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Description</label>
        <textarea name="description" rows="3" class="{{ $field }}">{{ old('description', $site->description ?? '') }}</textarea>
    </div>
    <div class="grid sm:grid-cols-2 gap-4 items-end">
        <div>
            <label class="block text-sm font-medium mb-1">Sort order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $site->sort_order ?? 0) }}"
                min="0" max="9999" class="{{ $field }}">
        </div>
        <label class="flex items-center gap-2 text-sm pb-2.5">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $site->is_active ?? true))> Active (visible on
            site)
        </label>
    </div>
</div>
