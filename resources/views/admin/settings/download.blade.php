@extends('admin.layouts.app')
@section('title', 'Download Settings')

@section('content')
    <form method="POST" action="{{ route('admin.settings.download.update') }}" class="max-w-2xl space-y-5">
        @csrf
        <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-6 space-y-5">
            <div>
                <label class="block text-sm font-medium mb-1">File retention (minutes)</label>
                <input type="number" name="retention_minutes" value="{{ old('retention_minutes', $retention) }}" min="5"
                    max="1440"
                    class="w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 px-3 py-2.5 outline-none focus:ring-2 focus:ring-violet-500">
                <p class="text-xs text-gray-500 mt-1">How long downloaded files stay available before automatic cleanup.</p>
                @error('retention_minutes')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Max file size (MB)</label>
                <input type="number" name="max_filesize_mb" value="{{ old('max_filesize_mb', $maxFilesize) }}"
                    min="10" max="10240"
                    class="w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 px-3 py-2.5 outline-none focus:ring-2 focus:ring-violet-500">
                @error('max_filesize_mb')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Max items per playlist</label>
                <input type="number" name="max_batch_items" value="{{ old('max_batch_items', $maxBatch) }}" min="1"
                    max="200"
                    class="w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 px-3 py-2.5 outline-none focus:ring-2 focus:ring-violet-500">
                @error('max_batch_items')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <label class="flex items-center gap-3">
                <input type="checkbox" name="enable_cookies" value="1" @checked(old('enable_cookies', $cookies))>
                <span class="text-sm">Allow authenticated downloads via the platform cookie pools</span>
            </label>
        </div>
        <button
            class="rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-semibold px-6 py-2.5 transition">Save
            settings</button>
    </form>
@endsection
