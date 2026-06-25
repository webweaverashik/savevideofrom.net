@extends('admin.layouts.app')
@section('title', 'Download Logs')

@section('content')
    <form method="GET" class="flex flex-wrap gap-3 mb-5">
        <select name="status"
            class="rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 px-3 py-2 text-sm">
            <option value="">All statuses</option>
            @foreach ($statuses as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select name="platform"
            class="rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 px-3 py-2 text-sm">
            <option value="">All platforms</option>
            @foreach ($platforms as $p)
                <option value="{{ $p }}" @selected(request('platform') === $p)>{{ ucfirst($p) }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-violet-600 hover:bg-violet-700 text-white text-sm px-4 py-2">Filter</button>
    </form>

    @if ($errorBreakdown->isNotEmpty())
        <div class="mb-5 flex flex-wrap gap-2">
            @foreach ($errorBreakdown as $e)
                <span
                    class="rounded-full border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] px-3 py-1.5 text-xs">
                    <span class="font-mono text-red-600 dark:text-red-400">{{ $e->error_type }}</span>
                    <span class="text-gray-400">·</span> {{ $e->c }}
                </span>
            @endforeach
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-xs text-gray-500 border-b border-gray-200/70 dark:border-white/10">
                    <tr>
                        <th class="px-4 py-3">When</th>
                        <th class="px-4 py-3">Platform</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Quality</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Title / Error</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $badge = [
                            'completed' =>
                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400',
                            'failed' => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
                            'processing' => 'bg-violet-100 text-violet-700 dark:bg-violet-500/15 dark:text-violet-400',
                        ];
                    @endphp
                    @forelse ($jobs as $job)
                        <tr class="border-b border-gray-100 dark:border-white/5">
                            <td class="px-4 py-3 whitespace-nowrap text-gray-500">{{ $job->created_at->diffForHumans() }}
                            </td>
                            <td class="px-4 py-3 capitalize">{{ $job->platform ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $job->media_type?->value ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $job->requested_quality ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-block rounded-full px-2 py-0.5 text-xs font-medium {{ $badge[$job->status->value] ?? 'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-300' }}">
                                    {{ $job->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 max-w-md">
                                @if ($job->status->value === 'failed')
                                    <div class="space-y-1">
                                        <span
                                            class="inline-block rounded px-2 py-0.5 text-xs font-mono bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400">{{ $job->error_type ?? 'error' }}</span>
                                        <p class="text-xs text-gray-500 line-clamp-2">{{ $job->error_message }}</p>
                                        <details class="text-xs">
                                            <summary class="cursor-pointer text-violet-600 dark:text-violet-400">URL
                                            </summary>
                                            <p class="mt-1 font-mono break-all text-gray-400">{{ $job->url }}</p>
                                        </details>
                                    </div>
                                @else
                                    <span
                                        class="text-gray-600 dark:text-gray-400 truncate block">{{ $job->title }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">No download jobs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $jobs->links() }}</div>
@endsection
