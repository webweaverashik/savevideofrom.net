<?php

declare (strict_types = 1);

return [

    /*
    |--------------------------------------------------------------------------
    | Binary paths (cross-platform)
    |--------------------------------------------------------------------------
    | On Laragon/Windows, "python" is typically correct. On Linux, "python3".
    | Leave ytdlp_path empty to invoke yt-dlp as a module: "python -m yt_dlp".
    */
    'python_path'       => env('PYTHON_PATH', PHP_OS_FAMILY === 'Windows' ? 'python' : 'python3'),
    'ytdlp_path'        => env('YTDLP_PATH', ''),
    'ffmpeg_path'       => env('FFMPEG_PATH', 'ffmpeg'),

    /*
    |--------------------------------------------------------------------------
    | Python worker scripts (implemented in Phase 3)
    |--------------------------------------------------------------------------
    */
    'scripts'           => [
        'extractor'  => base_path('python_worker/extractor.py'),
        'downloader' => base_path('python_worker/downloader.py'),
        'playlist'   => base_path('python_worker/playlist.py'),
    ],

    'cookies_path'      => storage_path('app/private/cookies'),

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    */
    'disk'              => env('DOWNLOAD_DISK', 'local'),
    'storage_path'      => storage_path('app/downloads'),

    /*
    |--------------------------------------------------------------------------
    | Limits & retention
    |--------------------------------------------------------------------------
    */
    'retention_minutes' => (int) env('DOWNLOAD_RETENTION_MINUTES', 60),
    'max_filesize_mb'   => (int) env('DOWNLOAD_MAX_FILESIZE_MB', 2048),
    'extract_timeout'   => (int) env('DOWNLOAD_EXTRACT_TIMEOUT', 60),
    'process_timeout'   => (int) env('DOWNLOAD_PROCESS_TIMEOUT', 600),

    /*
    |--------------------------------------------------------------------------
    | Queue names
    |--------------------------------------------------------------------------
    */
    'queues'            => [
        'extract'  => env('DOWNLOAD_QUEUE_EXTRACT', 'extract'),
        'download' => env('DOWNLOAD_QUEUE_DOWNLOAD', 'downloads'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    */
    'allowed_schemes'   => ['http', 'https'],

    /*
    |--------------------------------------------------------------------------
    | Max playlist videos
    |--------------------------------------------------------------------------
    */
    'max_batch_items'   => (int) env('DOWNLOAD_MAX_BATCH_ITEMS', 50),

    /*
    |--------------------------------------------------------------------------
    | Blocked videos
    |--------------------------------------------------------------------------
    */
    'blocked_hosts'     => [
        'pornhub.com', 'xvideos.com', 'xnxx.com', 'redtube.com', 'youporn.com',
        'xhamster.com', 'spankbang.com', 'youjizz.com', 'tube8.com', 'tnaflix.com',
        'porntrex.com', 'eporner.com', 'hclips.com', 'txxx.com', 'beeg.com',
        'chaturbate.com', 'stripchat.com', 'cam4.com', 'bongacams.com', 'myfreecams.com',
        'onlyfans.com', 'fansly.com', 'brazzers.com', 'naughtyamerica.com', 'rule34video.com', 'faphouse.com',
    ],
];
