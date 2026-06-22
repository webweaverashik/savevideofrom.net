<?php

declare(strict_types=1);

use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\DownloadController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');

// Async downloader API (web group → CSRF + session). Phase 8 adds SEO landing pages.
Route::post('/api/extract', [DownloadController::class, 'extract'])
    ->middleware('throttle:extract')
    ->name('api.extract');

Route::post('/api/download', [DownloadController::class, 'download'])
    ->middleware('throttle:download')
    ->name('api.download');

Route::get('/api/status/{job}', [DownloadController::class, 'status'])->name('api.status');

Route::get('/download/{job}', [DownloadController::class, 'serve'])->name('download.serve');


Route::post('/api/batch/extract', [BatchController::class, 'extract'])
    ->middleware('throttle:extract')->name('api.batch.extract');

Route::post('/api/batch/download', [BatchController::class, 'download'])
    ->middleware('throttle:download')->name('api.batch.download');

Route::get('/api/batch/status/{batch}', [BatchController::class, 'status'])->name('api.batch.status');

Route::get('/batch/{batch}/download', [BatchController::class, 'serve'])->name('batch.serve');