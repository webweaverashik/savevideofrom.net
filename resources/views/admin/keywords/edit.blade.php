@extends('admin.layouts.app')
@section('title', 'Keywords')

@section('content')
    <form method="POST" action="{{ route('admin.keywords.update') }}" class="max-w-2xl space-y-5">
        @csrf @method('PUT')
        <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-6">
            <label class="block text-sm font-medium mb-1">Popular search keywords</label>
            <p class="text-xs text-gray-500 mb-3">One keyword per line. Shown as the keyword cloud at the bottom of the
                homepage and landing pages. Leave empty to use defaults.</p>
            <textarea name="keywords" rows="12"
                class="w-full font-mono text-sm rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 px-3 py-2.5 outline-none focus:ring-2 focus:ring-violet-500">{{ old('keywords', $text) }}</textarea>
        </div>
        <button
            class="rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-semibold px-6 py-2.5 transition">Save
            keywords</button>
    </form>
@endsection
