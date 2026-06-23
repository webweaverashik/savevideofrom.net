<?php

declare (strict_types = 1);

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CookieController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DownloadLogController;
use App\Http\Controllers\Admin\KeywordController;
use App\Http\Controllers\Admin\LandingPageController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SupportedSiteController;
use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\DownloadController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use App\Models\Platform;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'platforms' => Platform::query()->active()
            ->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('name')->get(),
    ]);
})->name('home');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/robots.txt', function () {
    return response("User-agent: *\nAllow: /\nSitemap: " . url('/sitemap.xml') . "\n", 200, ['Content-Type' => 'text/plain']);
});

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

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.attempt');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('logs', [DownloadLogController::class, 'index'])->name('logs');

        Route::get('cookies', [CookieController::class, 'index'])->name('cookies.index');
        Route::get('cookies/{platform:slug}', [CookieController::class, 'show'])->name('cookies.show');
        Route::post('cookies/{platform:slug}', [CookieController::class, 'store'])->name('cookies.store');
        Route::delete('cookies/{platform:slug}/{file}', [CookieController::class, 'destroy'])->name('cookies.destroy');

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('download', [SettingsController::class, 'download'])->name('download');
            Route::post('download', [SettingsController::class, 'updateDownload'])->name('download.update');
            Route::get('ads', [SettingsController::class, 'ads'])->name('ads');
            Route::post('ads', [SettingsController::class, 'updateAds'])->name('ads.update');
            Route::get('seo', [SettingsController::class, 'seo'])->name('seo');
            Route::post('seo', [SettingsController::class, 'updateSeo'])->name('seo.update');
            Route::get('contact', [SettingsController::class, 'contact'])->name('contact');
            Route::post('contact', [SettingsController::class, 'updateContact'])->name('contact.update');
        });

        Route::get('landing-pages', [LandingPageController::class, 'index'])->name('landing.index');
        Route::get('landing-pages/{platform}/edit', [LandingPageController::class, 'edit'])->name('landing.edit');
        Route::put('landing-pages/{platform}', [LandingPageController::class, 'update'])->name('landing.update');

        Route::resource('sites', SupportedSiteController::class)->except(['show']);
        Route::resource('menus', MenuController::class)->except(['show']);

        Route::get('pages', [AdminPageController::class, 'index'])->name('pages.index');
        Route::get('pages/{page}/edit', [AdminPageController::class, 'edit'])->name('pages.edit');
        Route::put('pages/{page}', [AdminPageController::class, 'update'])->name('pages.update');

        Route::get('messages', [ContactMessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
        Route::delete('messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');

        Route::get('keywords', [KeywordController::class, 'edit'])->name('keywords.edit');
        Route::put('keywords', [KeywordController::class, 'update'])->name('keywords.update');
    });
});

Route::get('/privacy-policy', [PageController::class, 'show'])->defaults('slug', 'privacy-policy')->name('page.privacy');
Route::get('/terms-of-service', [PageController::class, 'show'])->defaults('slug', 'terms-of-service')->name('page.terms');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->middleware('throttle:contact')->name('contact.submit');

Route::get('/{page}', [LandingController::class, 'show'])
    ->where('page', '[a-z0-9-]+-downloader')
    ->name('landing');
