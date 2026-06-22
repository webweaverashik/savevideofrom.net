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
                            <td class="px-4 py-3 max-w-xs truncate text-gray-600 dark:text-gray-400">
                                {{ $job->status->value === 'failed' ? $job->error_message : $job->title }}
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
