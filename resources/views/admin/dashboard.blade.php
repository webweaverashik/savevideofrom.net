@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        @php
            $cards = [
                ['Total downloads', number_format($total), 'text-gray-900 dark:text-white'],
                ['Completed', number_format($completed), 'text-emerald-600 dark:text-emerald-400'],
                ['Failed', number_format($failed), 'text-red-600 dark:text-red-400'],
                ['Today', number_format($today), 'text-violet-600 dark:text-violet-400'],
                ['Playlists', number_format($batches), 'text-gray-900 dark:text-white'],
            ];
        @endphp
        @foreach ($cards as [$label, $value, $color])
            <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-4">
                <div class="text-xs text-gray-500">{{ $label }}</div>
                <div class="mt-1 text-2xl font-bold {{ $color }}">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div
            class="lg:col-span-2 rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold">Downloads — last 14 days</h2>
                <span class="text-xs text-gray-500">Success {{ $successRate }}% · Failed {{ $failureRate }}%</span>
            </div>
            <div class="flex items-end gap-1.5">
                @foreach ($days as $day)
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full h-44 flex items-end" title="{{ $day['date'] }}: {{ $day['count'] }}">
                            <div class="w-full rounded-t bg-gradient-to-t from-violet-600 to-fuchsia-500"
                                style="height: {{ $day['count'] ? max(4, round(($day['count'] / $maxDay) * 100)) : 2 }}%">
                            </div>
                        </div>
                        <span class="text-[10px] text-gray-400 mt-1">{{ (int) substr($day['date'], 8, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-5">
            <h2 class="font-semibold mb-4">Top platforms</h2>
            @forelse ($perPlatform as $p)
                <div class="flex items-center justify-between py-1.5 text-sm">
                    <span class="capitalize">{{ $p->platform }}</span>
                    <span class="text-gray-500">{{ number_format($p->c) }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-500">No downloads yet.</p>
            @endforelse
        </div>
    </div>
@endsection
