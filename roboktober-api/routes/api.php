<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/**
 * Roboktober REST API — v1
 *
 * All endpoints return JSON. Authentication via Sanctum (where required).
 * Public endpoints: posts, teams, links, pages, registratie
 *
 * @see PLAN.md §5.2 — REST API endpoint specification
 */
Route::prefix('v1')->name('api.v1.')->group(function (): void {
    require __DIR__.'/api/v1/public.php';

    Route::middleware(['auth:sanctum', '2fa.confirmed'])->group(function (): void {
        require __DIR__.'/api/v1/authenticated.php';
    });
});
