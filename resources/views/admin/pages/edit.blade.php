@extends('admin.layouts.app')
@section('title', 'Edit: ' . $page->title)

@section('content')
    <a href="{{ route('admin.pages.index') }}" class="text-sm text-violet-600 dark:text-violet-400 hover:underline">
        ← All pages</a>

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

    @php $field = 'w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 px-3 py-2.5 outline-none focus:ring-2 focus:ring-violet-500'; @endphp

    <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">

    <form method="POST" action="{{ route('admin.pages.update', $page) }}" id="pageForm" class="mt-4 max-w-3xl space-y-5">
        @csrf @method('PUT')
        <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-6 space-y-5">
            <div>
                <label class="block text-sm font-medium mb-1">Title</label>
                <input type="text" name="title" value="{{ old('title', $page->title) }}" class="{{ $field }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Content</label>
                <div id="editor" class="bg-white dark:bg-gray-900 rounded-b-xl">{!! old('body', $page->body) !!}</div>
                <textarea name="body" id="bodyInput" class="hidden"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Meta title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}"
                    class="{{ $field }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Meta description</label>
                <textarea name="meta_description" rows="2" class="{{ $field }}">{{ old('meta_description', $page->meta_description) }}</textarea>
            </div>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_published" value="1"
                    @checked(old('is_published', $page->is_published))> Published</label>
        </div>
        <button
            class="rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-semibold px-6 py-2.5 transition">Save
            page</button>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quill = new Quill('#editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{
                            header: [2, 3, 4, false]
                        }],
                        ['bold', 'italic', 'underline'],
                        [{
                            list: 'ordered'
                        }, {
                            list: 'bullet'
                        }],
                        ['blockquote', 'link'],
                        ['clean'],
                    ]
                },
            });

            const form = document.getElementById('pageForm');
            const input = document.getElementById('bodyInput');

            // Keep the hidden field in sync as you type…
            quill.on('text-change', function() {
                input.value = quill.root.innerHTML;
            });

            // …and once more right before submit, as a guarantee.
            form.addEventListener('submit', function() {
                const html = quill.root.innerHTML;
                input.value = (html === '<p><br></p>') ? '' : html;
            });
        });
    </script>
@endsection
