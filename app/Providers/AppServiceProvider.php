<?php
namespace App\Providers;

use App\Services\Download\Contracts\MediaDownloader;
use App\Services\Download\Contracts\MediaExtractor;
use App\Services\Download\YtDlpDownloader;
use App\Services\Download\YtDlpExtractor;
use App\Services\Settings\SettingsService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            MediaExtractor::class,
            YtDlpExtractor::class,
        );

        $this->app->bind(
            MediaDownloader::class,
            YtDlpDownloader::class,
        );

        $this->app->singleton(SettingsService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('extract', fn(Request $r) => Limit::perMinute(10)->by($r->ip()));
        RateLimiter::for('download', fn(Request $r) => Limit::perMinute(20)->by($r->ip()));
    }
}
