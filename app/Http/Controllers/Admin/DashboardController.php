<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Admin;

use App\Enums\DownloadStatus;
use App\Http\Controllers\Controller;
use App\Models\DownloadBatch;
use App\Models\DownloadJob;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $total     = DownloadJob::count();
        $completed = DownloadJob::where('status', DownloadStatus::Completed->value)->count();
        $failed    = DownloadJob::where('status', DownloadStatus::Failed->value)->count();
        $today     = DownloadJob::whereDate('created_at', today())->count();
        $batches   = DownloadBatch::count();

        $failureRate = $total > 0 ? round($failed / $total * 100, 1) : 0.0;
        $successRate = $total > 0 ? round($completed / $total * 100, 1) : 0.0;

        $perPlatform = DownloadJob::selectRaw('platform, COUNT(*) as c')
            ->whereNotNull('platform')
            ->groupBy('platform')
            ->orderByDesc('c')
            ->limit(8)
            ->get();

        // Per-platform success/failure breakdown
        $platformStats = DownloadJob::selectRaw('
                platform,
                COUNT(*) as total,
                SUM(status = ?) as completed,
                SUM(status = ?) as failed
            ', [DownloadStatus::Completed->value, DownloadStatus::Failed->value])
            ->whereNotNull('platform')
            ->groupBy('platform')
            ->orderByDesc('total')
            ->get()
            ->map(function ($row) {
                $row->success_rate = $row->total > 0 ? round($row->completed / $row->total * 100, 1) : 0.0;
                $row->failure_rate = $row->total > 0 ? round($row->failed / $row->total * 100, 1) : 0.0;
                return $row;
            });

        // Last 14 days, filling gaps in PHP.
        $rows = DownloadJob::selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('d')
            ->pluck('c', 'd');

        $days = [];
        for ($i = 13; $i >= 0; $i--) {
            $date   = now()->subDays($i)->toDateString();
            $days[] = ['date' => $date, 'count' => (int) ($rows[$date] ?? 0)];
        }
        $maxDay = max(array_column($days, 'count')) ?: 1;

        return view('admin.dashboard', compact(
            'total', 'completed', 'failed', 'today', 'batches',
            'failureRate', 'successRate', 'perPlatform', 'platformStats', 'days', 'maxDay',
        ));
    }
}
