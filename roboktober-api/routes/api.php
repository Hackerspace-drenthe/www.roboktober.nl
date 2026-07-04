<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\LinkController;
use App\Http\Controllers\Api\V1\EditionController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\RegistratieController;
use App\Http\Controllers\Api\V1\TeamController;
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
    // Blog posts (public, read-only)
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/{slug}', [PostController::class, 'show'])->name('posts.show');

    // Approved teams (public, read-only)
    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
    Route::get('/teams/{id}', [TeamController::class, 'show'])->name('teams.show');
    Route::get('/teams/{id}/robots', [TeamController::class, 'robots'])->name('teams.robots');

    // Build Hub links (public, read-only)
    Route::get('/links', [LinkController::class, 'index'])->name('links.index');

    // Event editions (public, read-only)
    Route::get('/edities', [EditionController::class, 'index'])->name('editions.index');

    // CMS pages (public, read-only)
    Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

    // Team registration (public, write)
    Route::post('/registratie', [RegistratieController::class, 'store'])
        ->middleware('throttle:registratie')
        ->name('registratie.store');
});
