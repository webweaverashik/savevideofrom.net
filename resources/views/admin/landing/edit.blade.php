@extends('admin.layouts.app')
@section('title', 'Edit: ' . $platform->name)

@section('content')
    <a href="{{ route('admin.landing.index') }}" class="text-sm text-violet-600 dark:text-violet-400 hover:underline">
        ← All landing pages</a>

    @if ($errors->any())
        <div
            class="mt-4 rounded-xl border border-red-300 bg-red-50 dark:bg-red-950/30 dark:border-red-900 text-red-700 dark:text-red-300 px-4 py-3 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $field =
            'w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 px-3 py-2.5 outline-none focus:ring-2 focus:ring-violet-500';
        $rowCls = 'repeater-row flex gap-2 items-start';
        $smInput =
            'flex-1 rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-violet-500';
        $rmBtn = 'remove-row shrink-0 mt-1 text-sm text-red-600 hover:text-red-700 px-2 py-1';
    @endphp

    <form method="POST" action="{{ route('admin.landing.update', $platform) }}" class="mt-4 max-w-3xl space-y-6">
        @csrf @method('PUT')

        {{-- Page --}}
        <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-6 space-y-5">
            <h2 class="font-semibold">Page</h2>
            <div>
                <label class="block text-sm font-medium mb-1">URL slug</label>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-400">{{ url('/') }}/</span>
                    <input type="text" name="page_slug" value="{{ old('page_slug', $platform->page_slug) }}"
                        class="{{ $field }}">
                </div>
                <p class="text-xs text-gray-500 mt-1">Lowercase words with hyphens, ending in <code>-downloader</code>.</p>
            </div>
            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Sort order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $platform->sort_order) }}"
                        min="0" max="999" class="{{ $field }}">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Brand color</label>
                    <input type="color" name="color" value="{{ old('color', $platform->color ?: '#7c3aed') }}"
                        class="h-11 w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900">
                </div>
            </div>
            <div class="flex flex-wrap gap-5">
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_published" value="1"
                        @checked(old('is_published', $platform->is_published))> Published</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1"
                        @checked(old('is_active', $platform->is_active))> Active</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_featured" value="1"
                        @checked(old('is_featured', $platform->is_featured))> Featured on homepage</label>
            </div>
        </div>

        {{-- SEO --}}
        <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-6 space-y-5">
            <h2 class="font-semibold">SEO &amp; content</h2>
            <div>
                <label class="block text-sm font-medium mb-1">Meta title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $platform->meta_title) }}"
                    class="{{ $field }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Meta description</label>
                <textarea name="meta_description" rows="2" class="{{ $field }}">{{ old('meta_description', $platform->meta_description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">H1 heading</label>
                <input type="text" name="h1" value="{{ old('h1', $platform->h1) }}" class="{{ $field }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Intro paragraph</label>
                <textarea name="intro" rows="3" class="{{ $field }}">{{ old('intro', $platform->intro) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Popular-websites card description</label>
                <textarea name="card_description" rows="2" class="{{ $field }}">{{ old('card_description', $platform->card_description) }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Short blurb shown in the “Popular Websites” grid on the homepage and
                    landing pages. Leave blank to hide this platform from that grid.</p>
            </div>
        </div>

        {{-- How-to --}}
        <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-6">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-semibold">How-to steps</h2>
                <button type="button" id="addHowto"
                    class="text-sm rounded-lg bg-violet-600 hover:bg-violet-700 text-white px-3 py-1.5">+ Add step</button>
            </div>
            <div id="howtoRows" class="space-y-2">
                @foreach (old('howto', $platform->howto ?? []) as $i => $step)
                    <div class="{{ $rowCls }}">
                        <input type="text" name="howto[{{ $i }}][title]" value="{{ $step['title'] ?? '' }}"
                            placeholder="Step title" class="{{ $smInput }}">
                        <input type="text" name="howto[{{ $i }}][text]" value="{{ $step['text'] ?? '' }}"
                            placeholder="Step description" class="{{ $smInput }}">
                        <button type="button" class="{{ $rmBtn }}">✕</button>
                    </div>
                @endforeach
            </div>
            <template id="howtoTemplate">
                <div class="{{ $rowCls }}">
                    <input type="text" name="howto[__I__][title]" placeholder="Step title" class="{{ $smInput }}">
                    <input type="text" name="howto[__I__][text]" placeholder="Step description"
                        class="{{ $smInput }}">
                    <button type="button" class="{{ $rmBtn }}">✕</button>
                </div>
            </template>
        </div>

        {{-- FAQ --}}
        <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-6">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-semibold">FAQ</h2>
                <button type="button" id="addFaq"
                    class="text-sm rounded-lg bg-violet-600 hover:bg-violet-700 text-white px-3 py-1.5">+ Add
                    question</button>
            </div>
            <div id="faqRows" class="space-y-3">
                @foreach (old('faqs', $platform->faqs ?? []) as $i => $faq)
                    <div class="repeater-row rounded-xl border border-gray-200 dark:border-white/10 p-3 space-y-2">
                        <div class="flex gap-2 items-start">
                            <input type="text" name="faqs[{{ $i }}][q]" value="{{ $faq['q'] ?? '' }}"
                                placeholder="Question" class="{{ $smInput }}">
                            <button type="button" class="{{ $rmBtn }}">✕</button>
                        </div>
                        <textarea name="faqs[{{ $i }}][a]" rows="2" placeholder="Answer"
                            class="{{ $smInput }} w-full">{{ $faq['a'] ?? '' }}</textarea>
                    </div>
                @endforeach
            </div>
            <template id="faqTemplate">
                <div class="repeater-row rounded-xl border border-gray-200 dark:border-white/10 p-3 space-y-2">
                    <div class="flex gap-2 items-start">
                        <input type="text" name="faqs[__I__][q]" placeholder="Question" class="{{ $smInput }}">
                        <button type="button" class="{{ $rmBtn }}">✕</button>
                    </div>
                    <textarea name="faqs[__I__][a]" rows="2" placeholder="Answer" class="{{ $smInput }} w-full"></textarea>
                </div>
            </template>
        </div>

        <button
            class="rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-semibold px-6 py-2.5 transition">Save
            landing page</button>
    </form>

    <script>
        (function() {
            let counter =
                {{ max(count(old('howto', $platform->howto ?? [])), count(old('faqs', $platform->faqs ?? []))) + 1 }};

            function repeater(addBtnId, rowsId, templateId) {
                const addBtn = document.getElementById(addBtnId);
                const rows = document.getElementById(rowsId);
                const tpl = document.getElementById(templateId);

                addBtn?.addEventListener('click', () => {
                    const html = tpl.innerHTML.replace(/__I__/g, counter++);
                    const wrap = document.createElement('div');
                    wrap.innerHTML = html.trim();
                    rows.appendChild(wrap.firstChild);
                });

                rows?.addEventListener('click', (e) => {
                    if (e.target.closest('.remove-row')) {
                        e.target.closest('.repeater-row')?.remove();
                    }
                });
            }

            repeater('addHowto', 'howtoRows', 'howtoTemplate');
            repeater('addFaq', 'faqRows', 'faqTemplate');
        })();
    </script>
@endsection
