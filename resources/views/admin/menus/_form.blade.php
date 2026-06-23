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
        <label class="block text-sm font-medium mb-1">Label</label>
        <input type="text" name="label" value="{{ old('label', $menu->label ?? '') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">URL <span class="text-gray-400">(leave blank for a dropdown / column
                heading)</span></label>
        <input type="text" name="url" value="{{ old('url', $menu->url ?? '') }}"
            placeholder="/youtube-video-downloader or https://…" class="{{ $field }}">
    </div>
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Location</label>
            <select name="location" class="{{ $field }}">
                <option value="header" @selected(old('location', $menu->location ?? 'header') === 'header')>Header</option>
                <option value="footer" @selected(old('location', $menu->location ?? '') === 'footer')>Footer</option>
            </select>
            <p class="text-xs text-gray-500 mt-1">Ignored if a parent is selected (children follow their parent).</p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Parent <span class="text-gray-400">(optional)</span></label>
            <select name="parent_id" class="{{ $field }}">
                <option value="">— Top level —</option>
                @foreach ($parents as $p)
                    <option value="{{ $p->id }}" @selected((int) old('parent_id', $menu->parent_id ?? 0) === $p->id)>[{{ $p->location }}]
                        {{ $p->label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="grid sm:grid-cols-2 gap-4 items-end">
        <div>
            <label class="block text-sm font-medium mb-1">Sort order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $menu->sort_order ?? 0) }}"
                min="0" max="999" class="{{ $field }}">
        </div>
        <div class="flex flex-col gap-2 pb-1">
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1"
                    @checked(old('is_active', $menu->is_active ?? true))> Active</label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="open_new_tab" value="1"
                    @checked(old('open_new_tab', $menu->open_new_tab ?? false))> Open in new tab</label>
        </div>
    </div>
</div>
