<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\LinkController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Admin\PageModerationController;
use App\Http\Controllers\Api\V1\Admin\PostModerationController;
use App\Http\Controllers\Api\V1\Admin\TeamModerationController;
use App\Http\Controllers\Api\V1\Admin\TeamUpdateModerationController;
use App\Http\Controllers\Api\V1\Admin\UserManagementController;
use App\Http\Controllers\Api\V1\Admin\AuditLogController;
use App\Http\Controllers\Api\V1\Admin\AdminDashboardController;
use App\Http\Controllers\Api\V1\EditionController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\RegistratieController;
use App\Http\Controllers\Api\V1\RichMediaController;
use App\Http\Controllers\Api\V1\TeamRegistrationUpdateController;
use App\Http\Controllers\Api\V1\TeamRegistrationEditController;
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
    // Auth (public)
    Route::post('/auth/register', [AuthController::class, 'register'])
        ->middleware('throttle:6,1')
        ->name('auth.register');

    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('auth.login');

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

    // Team registration (authenticated, write)
    Route::post('/registratie', [RegistratieController::class, 'store'])
        ->middleware(['auth:sanctum', 'throttle:registratie'])
        ->name('registratie.store');

    Route::get('/registratie/{token}', [TeamRegistrationEditController::class, 'show'])
        ->middleware(['throttle:registratie', 'registration.edit-token'])
        ->name('registratie.show');

    Route::put('/registratie/{token}', [TeamRegistrationEditController::class, 'update'])
        ->middleware(['auth:sanctum', 'throttle:registratie', 'registration.edit-token'])
        ->name('registratie.update');

    Route::get('/registratie/{token}/updates', [TeamRegistrationUpdateController::class, 'index'])
        ->middleware(['throttle:registratie', 'registration.edit-token'])
        ->name('registratie.updates.index');

    Route::post('/registratie/{token}/updates', [TeamRegistrationUpdateController::class, 'store'])
        ->middleware(['auth:sanctum', 'throttle:registratie', 'registration.edit-token'])
        ->name('registratie.updates.store');

    // Authenticated user API
    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('/auth/claim-team', [AuthController::class, 'claimTeam'])
            ->name('auth.claim-team');
        Route::post('/auth/team-edit-link', [AuthController::class, 'teamEditLink'])
            ->name('auth.team-edit-link');

        Route::prefix('media')->name('media.')->middleware('role:teamcaptain,moderator,admin')->group(function (): void {
            Route::get('/', [RichMediaController::class, 'index'])->name('index');
            Route::post('/upload', [RichMediaController::class, 'upload'])->name('upload');
            Route::post('/{media}/attach', [RichMediaController::class, 'attach'])->name('attach');
        });

        // First admin API-only module: team moderation
        Route::prefix('admin')->name('admin.')->middleware('role:moderator,admin')->group(function (): void {
            Route::get('/dashboard-summary', [AdminDashboardController::class, 'summary'])->name('dashboard.summary');

            Route::get('/teams', [TeamModerationController::class, 'index'])->name('teams.index');
            Route::get('/teams/{team}', [TeamModerationController::class, 'show'])->name('teams.show');
            Route::patch('/teams/{team}/status', [TeamModerationController::class, 'updateStatus'])
                ->name('teams.update-status');

            Route::get('/posts', [PostModerationController::class, 'index'])->name('posts.index');
            Route::get('/posts/{post:id}', [PostModerationController::class, 'show'])->name('posts.show');
            Route::patch('/posts/{post:id}/content', [PostModerationController::class, 'updateContent'])
                ->name('posts.update-content');
            Route::patch('/posts/{post:id}/status', [PostModerationController::class, 'updateStatus'])
                ->name('posts.update-status');

            Route::get('/pages', [PageModerationController::class, 'index'])->name('pages.index');
            Route::get('/pages/{page}', [PageModerationController::class, 'show'])->name('pages.show');
            Route::patch('/pages/{page}/content', [PageModerationController::class, 'updateContent'])
                ->name('pages.update-content');
            Route::patch('/pages/{page}/status', [PageModerationController::class, 'updateStatus'])
                ->name('pages.update-status');

            Route::get('/team-updates', [TeamUpdateModerationController::class, 'index'])->name('team-updates.index');
            Route::get('/team-updates/{teamUpdate}', [TeamUpdateModerationController::class, 'show'])->name('team-updates.show');
            Route::patch('/team-updates/{teamUpdate}/content', [TeamUpdateModerationController::class, 'updateContent'])
                ->name('team-updates.update-content');
            Route::patch('/team-updates/{teamUpdate}/status', [TeamUpdateModerationController::class, 'updateStatus'])
                ->name('team-updates.update-status');

            Route::prefix('users')->name('users.')->middleware('role:admin')->group(function (): void {
                Route::get('/', [UserManagementController::class, 'index'])->name('index');
                Route::patch('/{user}/role', [UserManagementController::class, 'updateRole'])->name('update-role');
            });

            Route::prefix('audit-logs')->name('audit-logs.')->middleware('role:admin')->group(function (): void {
                Route::get('/', [AuditLogController::class, 'index'])->name('index');
            });
        });
    });
});
