<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Admin;

use App\Enums\DownloadStatus;
use App\Http\Controllers\Controller;
use App\Models\DownloadJob;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DownloadLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = DownloadJob::query()->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($platform = $request->query('platform')) {
            $query->where('platform', $platform);
        }

        $jobs = $query->paginate(25)->withQueryString();

        $platforms      = DownloadJob::whereNotNull('platform')->distinct()->orderBy('platform')->pluck('platform');
        $statuses       = array_map(static fn($c) => $c->value, DownloadStatus::cases());
        $errorBreakdown = DownloadJob::selectRaw('error_type, COUNT(*) as c')
            ->where('status', DownloadStatus::Failed->value)
            ->when($request->query('platform'), fn($q, $p) => $q->where('platform', $p))
            ->whereNotNull('error_type')
            ->groupBy('error_type')
            ->orderByDesc('c')
            ->get();

        return view('admin.logs', compact('jobs', 'platforms', 'statuses', 'errorBreakdown'));
    }
}
