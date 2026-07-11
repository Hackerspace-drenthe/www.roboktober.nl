<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Uploads\MediaStorage;
use App\Models\User;
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
            if ($request->routeIs('api.v1.registratie.store') || $request->routeIs('registratie.store')) {
                $emailInput = $request->input('email');
                $email = is_string($emailInput) ? mb_strtolower($emailInput) : '';

                return [
                    Limit::perMinute(5)->by('registratie-store-ip:'.$request->ip()),
                    Limit::perHour(20)->by('registratie-store-email:'.$email),
                ];
            }

            $actor = $request->user();
            $user = $actor instanceof User ? $actor : null;

            if ($user !== null) {
                return [
                    Limit::perMinute(60)->by('registratie-account-user:'.$user->id),
                    Limit::perHour(600)->by('registratie-account-user:'.$user->id),
                ];
            }

            return [
                Limit::perMinute(30)->by('registratie-account-ip:'.$request->ip()),
                Limit::perHour(300)->by('registratie-account-ip:'.$request->ip()),
            ];
        });
    }
}
