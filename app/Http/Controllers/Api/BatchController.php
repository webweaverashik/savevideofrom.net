<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Api;

use App\Enums\DownloadStatus;
use App\Enums\MediaType;
use App\Exceptions\ExtractionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\BatchDownloadRequest;
use App\Http\Requests\BatchExtractRequest;
use App\Jobs\FinalizeBatchJob;
use App\Jobs\ProcessDownloadJob;
use App\Models\DownloadBatch;
use App\Models\DownloadJob;
use App\Services\Download\DownloadSettings;
use App\Services\Download\PlatformDetector;
use App\Services\Download\PlaylistExtractor;
use Illuminate\Bus\Batch;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BatchController extends Controller
{
    public function __construct(
        private readonly PlatformDetector $detector,
        private readonly PlaylistExtractor $playlist,
        private readonly DownloadSettings $settings,
    ) {}

    public function extract(BatchExtractRequest $request): JsonResponse
    {
        try {
            $data = $this->playlist->extract($request->validated('url'));
        } catch (ExtractionException $e) {
            return response()->json(['error' => $e->getMessage(), 'error_type' => $e->errorType], 422);
        }

        $max     = $this->settings->maxBatchItems();
        $entries = array_slice($data['entries'] ?? [], 0, $max);

        return response()->json([
            'title'     => $data['title'] ?? 'Playlist',
            'platform'  => $data['platform'] ?? null,
            'count'     => count($entries),
            'truncated' => ($data['count'] ?? 0) > $max,
            'entries'   => $entries,
        ]);
    }

    public function download(BatchDownloadRequest $request): JsonResponse
    {
        $urls      = array_values(array_unique($request->validated('urls')));
        $mediaType = $request->validated('media_type') === 'audio' ? MediaType::Audio : MediaType::Video;

        $batch = DownloadBatch::create([
            'source_url'        => $request->validated('url'),
            'platform'          => $this->detector->detect($request->validated('url')),
            'title'             => $request->validated('title') ?: 'Playlist',
            'total_items'       => count($urls),
            'requested_quality' => $request->validated('quality'),
            'requested_format'  => $request->validated('format'),
            'media_type'        => $mediaType,
            'ip_hash'           => hash('sha256', (string) $request->ip()),
        ]);

        $jobs = [];
        foreach ($urls as $entryUrl) {
            $child = DownloadJob::create([
                'url'               => $entryUrl,
                'platform'          => $this->detector->detect($entryUrl),
                'batch_id'          => $batch->id,
                'media_type'        => $mediaType,
                'requested_quality' => $batch->requested_quality,
                'requested_format'  => $batch->requested_format,
                'ip_hash'           => $batch->ip_hash,
            ]);
            $jobs[] = new ProcessDownloadJob($child->uuid);
        }

        $batchUuid = $batch->uuid;

        $bus = Bus::batch($jobs)
            ->name("svf-{$batchUuid}")
            ->allowFailures()
            ->finally(function (Batch $b) use ($batchUuid): void {
                FinalizeBatchJob::dispatch($batchUuid)->onQueue(config('downloader.queues.download'));
            })
            ->onQueue(config('downloader.queues.download'))
            ->dispatch();

        $batch->update(['bus_batch_id' => $bus->id]);

        return response()->json([
            'uuid'   => $batch->uuid,
            'status' => DownloadStatus::Processing->value,
            'total'  => count($urls),
        ]);
    }

    public function status(DownloadBatch $batch): JsonResponse
    {
        if ($batch->status === DownloadStatus::Completed && $batch->isExpired()) {
            return response()->json(['uuid' => $batch->uuid, 'status' => 'expired', 'error' => 'This batch has expired.']);
        }

        $base = ['uuid' => $batch->uuid, 'status' => $batch->status->value, 'total' => $batch->total_items];

        if ($batch->status === DownloadStatus::Completed) {
            return response()->json($base + [
                'download_url' => route('batch.serve', $batch->uuid),
                'zip_size'     => $batch->zip_size,
                'title'        => $batch->title,
            ]);
        }

        if ($batch->status === DownloadStatus::Failed) {
            return response()->json($base + ['error' => $batch->error_message ?: 'The batch failed.']);
        }

        $completed = 0;
        if ($batch->bus_batch_id && ($busBatch = Bus::findBatch($batch->bus_batch_id))) {
            $completed = $busBatch->processedJobs();
        }

        return response()->json($base + ['completed' => $completed]);
    }

    public function serve(DownloadBatch $batch): StreamedResponse
    {
        abort_unless($batch->status === DownloadStatus::Completed, 404);
        abort_if($batch->isExpired(), 410, 'This batch has expired.');

        $disk = Storage::disk(config('downloader.disk'));
        abort_unless($batch->zip_path && $disk->exists($batch->zip_path), 404);

        $name = Str::slug(Str::limit((string) ($batch->title ?: 'playlist'), 60, '')) ?: 'playlist';

        return $disk->download($batch->zip_path, $name . '.zip', ['Content-Type' => 'application/zip']);
    }
}
