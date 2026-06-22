@extends('admin.layouts.app')
@section('title', 'Cookies')

@section('content')
    <p class="text-sm text-gray-500 mb-6">
        Upload <code class="text-xs">cookies.txt</code> files per platform. When a public download fails,
        the worker automatically retries with a random cookie from that platform's pool.
    </p>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($platforms as $platform)
            <a href="{{ route('admin.cookies.show', $platform->slug) }}"
                class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-4 hover:border-violet-500/50 transition flex items-center justify-between">
                <div>
                    <div class="font-semibold">{{ $platform->name }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">{{ $platform->slug }}</div>
                </div>
                <span
                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $platform->cookie_count > 0 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400' : 'bg-gray-100 text-gray-500 dark:bg-white/10 dark:text-gray-400' }}">
                    {{ $platform->cookie_count }} {{ Str::plural('cookie', $platform->cookie_count) }}
                </span>
            </a>
        @endforeach
    </div>
@endsection
