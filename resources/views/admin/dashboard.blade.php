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

    <div
        class="mt-6 rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-gray-200/70 dark:border-white/10">
            <h2 class="font-semibold">Success &amp; failure by platform</h2>
            <a href="{{ route('admin.logs', ['status' => 'failed']) }}"
                class="text-sm text-violet-600 dark:text-violet-400 hover:underline">View failed downloads →</a>
        </div>
        <div class="overflow-x-auto px-15">
            <table class="w-full text-sm">
                <thead class="text-left text-xs text-gray-500 border-b border-gray-200/70 dark:border-white/10">
                    <tr>
                        <th class="px-4 py-3">Platform</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Completed</th>
                        <th class="px-4 py-3">Failed</th>
                        <th class="px-4 py-3 w-48">Success rate</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($platformStats as $p)
                        @php
                            $rate = $p->success_rate;
                            $bar = $rate >= 80 ? 'bg-emerald-500' : ($rate >= 50 ? 'bg-amber-500' : 'bg-red-500');
                        @endphp
                        <tr class="border-b border-gray-100 dark:border-white/5">
                            <td class="px-4 py-3 font-medium capitalize">{{ $p->platform }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ number_format($p->total) }}</td>
                            <td class="px-4 py-3 text-emerald-600 dark:text-emerald-400">{{ number_format($p->completed) }}
                            </td>
                            <td class="px-4 py-3 text-red-600 dark:text-red-400">{{ number_format($p->failed) }}</td>
                            @php
                                $rate = $p->success_rate;
                                $color = $rate >= 80 ? '#10b981' : ($rate >= 50 ? '#f59e0b' : '#ef4444');
                            @endphp
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-2 rounded-full bg-gray-200 dark:bg-white/10 overflow-hidden">
                                        <div class="h-full rounded-full"
                                            style="width: {{ $rate }}%; background-color: {{ $color }};">
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-500 w-10 text-right">{{ $rate }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.logs', ['platform' => $p->platform, 'status' => 'failed']) }}"
                                    class="text-xs text-violet-600 dark:text-violet-400 hover:underline whitespace-nowrap">Debug
                                    →</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-gray-500">No download data yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
