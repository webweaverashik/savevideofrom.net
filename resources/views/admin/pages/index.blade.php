@extends('admin.layouts.app')
@section('title', 'Pages')

@section('content')
    <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] overflow-hidden">
        <table class="w-full text-sm">
            <thead class="text-left text-xs text-gray-500 border-b border-gray-200/70 dark:border-white/10">
                <tr>
                    <th class="px-4 py-3">Title</th>
                    <th class="px-4 py-3">URL</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pages as $page)
                    @php
                        $url = match ($page->slug) {
                            'privacy-policy' => route('page.privacy'),
                            'terms-of-service' => route('page.terms'),
                            'contact' => route('contact'),
                            default => null,
                        };
                    @endphp
                    <tr class="border-b border-gray-100 dark:border-white/5">
                        <td class="px-4 py-3 font-medium">{{ $page->title }}</td>
                        <td class="px-4 py-3">
                            @if ($url)
                                <a href="{{ $url }}" target="_blank" rel="noopener"
                                    class="font-mono text-xs text-violet-600 dark:text-violet-400 hover:underline">/{{ $page->slug }}</a>
                            @endif
                        </td>
                        <td class="px-4 py-3"><span
                                class="inline-block rounded-full px-2 py-0.5 text-xs font-medium {{ $page->is_published ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400' : 'bg-gray-100 text-gray-500 dark:bg-white/10 dark:text-gray-400' }}">{{ $page->is_published ? 'Published' : 'Draft' }}</span>
                        </td>
                        <td class="px-4 py-3 text-right"><a href="{{ route('admin.pages.edit', $page) }}"
                                class="text-violet-600 dark:text-violet-400 hover:underline">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
