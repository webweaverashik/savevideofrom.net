<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Api;

use App\Enums\DownloadStatus;
use App\Enums\MediaType;
use App\Http\Controllers\Controller;
use App\Http\Requests\DownloadRequest;
use App\Http\Requests\ExtractRequest;
use App\Jobs\ExtractMetadataJob;
use App\Jobs\ProcessDownloadJob;
use App\Models\DownloadJob;
use App\Services\Download\CookieStore;
use App\Services\Download\PlatformDetector;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function __construct(
        private readonly PlatformDetector $detector,
        private readonly CookieStore $cookies,
    ) {}
    /** Phase 1: queue metadata extraction. */
    public function extract(ExtractRequest $request): JsonResponse
    {
        $url = $request->validated('url');

        $job = DownloadJob::create([
            'url'        => $url,
            'platform'   => $this->detector->detect($url),
            'ip_hash'    => hash('sha256', (string) $request->ip()),
            'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
        ]);

        if ($cookies = $request->validated('cookies')) {
            $this->cookies->store($job->uuid, $cookies);
            $job->update(['has_cookies' => true]);
        }

        ExtractMetadataJob::dispatch($job->uuid)
            ->onQueue(config('downloader.queues.extract'));

        return response()->json([
            'uuid'       => $job->uuid,
            'status'     => $job->status->value,
            'status_url' => route('api.status', $job->uuid),
        ]);
    }

    /** Poll target for both phases. */
    public function status(DownloadJob $job): JsonResponse
    {
        return response()->json($this->serialize($job));
    }

    /** Phase 2: record the chosen format and queue the download. */
    public function download(DownloadRequest $request): JsonResponse
    {
        $job = DownloadJob::where('uuid', $request->validated('uuid'))->firstOrFail();

        // A download is already running for this job — don't start a competing one.
        if ($job->status === DownloadStatus::Processing) {
            return response()->json($this->serialize($job), 409);
        }

        // Allow downloading from a freshly-extracted (Ready) job, and re-downloading
        // a different format from a previously Completed job.
        if (! in_array($job->status, [DownloadStatus::Ready, DownloadStatus::Completed], true)) {
            return response()->json([
                'uuid'   => $job->uuid,
                'status' => $job->status->value,
                'error'  => 'This request is not ready to download yet.',
            ], 409);
        }

        $audioOnly = $request->validated('media_type') === 'audio';

        $job->update([
            'format_id'         => $request->validated('format_id'),
            'requested_quality' => $request->validated('quality'),
            'requested_format'  => $request->validated('format'),
            'media_type'        => $audioOnly ? MediaType::Audio : MediaType::Video,
            'status'            => DownloadStatus::Processing, // claim synchronously so polling never sees a stale "completed"
            'file_path'         => null,
            'file_name'         => null,
            'error_type'        => null,
            'error_message'     => null,
        ]);

        ProcessDownloadJob::dispatch($job->uuid)->onQueue(config('downloader.queues.download'));

        return response()->json(['uuid' => $job->uuid, 'status' => DownloadStatus::Processing->value]);
    }

    /** Stream the finished file (memory-safe), enforcing completion + expiry. */
    public function serve(DownloadJob $job): StreamedResponse
    {
        abort_unless($job->status === DownloadStatus::Completed, 404);
        abort_if($job->isExpired(), 410, 'This download has expired.');

        $disk = Storage::disk(config('downloader.disk'));
        abort_unless($job->file_path && $disk->exists($job->file_path), 404);

        $ext   = pathinfo((string) $job->file_name, PATHINFO_EXTENSION);
        $name  = Str::slug(Str::limit((string) ($job->title ?: 'download'), 60, '')) ?: 'download';
        $name .= $ext ? '.' . $ext : '';

        return $disk->download(
            $job->file_path,
            $name,
            array_filter(['Content-Type' => $job->mime_type]),
        );
    }

    private function serialize(DownloadJob $job): array
    {
        $base = ['uuid' => $job->uuid, 'status' => $job->status->value];

        if ($job->status === DownloadStatus::Completed && $job->isExpired()) {
            return ['uuid' => $job->uuid, 'status' => 'expired', 'error' => 'This download has expired. Please start again.'];
        }

        return match ($job->status) {
            DownloadStatus::Ready     => [
                 ...$base,
                'title'     => $job->title,
                'thumbnail' => $job->thumbnail_url,
                'duration'  => $job->duration,
                'uploader'  => $job->meta['uploader'] ?? null,
                'platform'  => $job->platform,
                'formats'   => $job->meta['formats'] ?? [],
            ],
            DownloadStatus::Completed => [
                 ...$base,
                'title'        => $job->title,
                'download_url' => route('download.serve', $job->uuid),
                'file_name'    => $job->file_name,
                'file_size'    => $job->file_size,
                'media_type'   => $job->media_type?->value,
            ],
            DownloadStatus::Failed, DownloadStatus::Expired => [
                 ...$base,
                'error_type' => $job->error_type,
                'error'      => $job->error_message ?: 'Something went wrong. Please try again.',
            ],
            default                   => $base, // pending | extracting | processing
        };
    }
}
