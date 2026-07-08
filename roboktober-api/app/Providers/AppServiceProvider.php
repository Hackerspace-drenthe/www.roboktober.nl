<?php

namespace App\Providers;

use App\Contracts\Uploads\MediaStorage;
use App\Services\Uploads\FilesystemMediaStorage;
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
        $this->app->bind(MediaStorage::class, FilesystemMediaStorage::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('registratie', function (Request $request): array {
            $emailInput = $request->input('email');
            $email = is_string($emailInput) ? mb_strtolower($emailInput) : '';

            return [
                Limit::perMinute(5)->by('registratie-ip:'.$request->ip()),
                Limit::perHour(20)->by('registratie-email:'.$email),
            ];
        });
    }
}
