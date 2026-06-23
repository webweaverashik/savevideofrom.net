@extends('admin.layouts.app')
@section('title', 'Landing Pages')

@section('content')
    <p class="text-sm text-gray-500 mb-5">Each platform has an SEO landing page at <code class="text-xs">/{slug}</code>. Edit
        its content, meta, and visibility here.</p>

    <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-xs text-gray-500 border-b border-gray-200/70 dark:border-white/10">
                    <tr>
                        <th class="px-4 py-3">Platform</th>
                        <th class="px-4 py-3">URL</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($platforms as $platform)
                        <tr class="border-b border-gray-100 dark:border-white/5">
                            <td class="px-4 py-3 font-medium">{{ $platform->name }}</td>
                            <td class="px-4 py-3">
                                @if ($platform->is_published)
                                    <a href="{{ $platform->landingUrl() }}" target="_blank" rel="noopener"
                                        class="font-mono text-xs text-violet-600 dark:text-violet-400 hover:underline">/{{ $platform->page_slug }}</a>
                                @else
                                    <span class="font-mono text-xs text-gray-400">/{{ $platform->page_slug }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-block rounded-full px-2 py-0.5 text-xs font-medium {{ $platform->is_published ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400' : 'bg-gray-100 text-gray-500 dark:bg-white/10 dark:text-gray-400' }}">
                                    {{ $platform->is_published ? 'Published' : 'Draft' }}
                                </span>
                                @if ($platform->is_featured)
                                    <span
                                        class="ml-1 inline-block rounded-full px-2 py-0.5 text-xs font-medium bg-violet-100 text-violet-700 dark:bg-violet-500/15 dark:text-violet-400">Featured</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.landing.edit', $platform) }}"
                                    class="text-sm text-violet-600 dark:text-violet-400 hover:underline">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
