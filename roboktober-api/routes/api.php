<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\LinkController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Admin\PageModerationController;
use App\Http\Controllers\Api\V1\Admin\PostModerationController;
use App\Http\Controllers\Api\V1\Admin\RobotManagementController;
use App\Http\Controllers\Api\V1\Admin\LinkManagementController;
use App\Http\Controllers\Api\V1\Admin\LocationManagementController;
use App\Http\Controllers\Api\V1\Admin\TeamModerationController;
use App\Http\Controllers\Api\V1\Admin\TeamUpdateModerationController;
use App\Http\Controllers\Api\V1\Admin\UserManagementController;
use App\Http\Controllers\Api\V1\Admin\AuditLogController;
use App\Http\Controllers\Api\V1\Admin\AdminDashboardController;
use App\Http\Controllers\Api\V1\Admin\CompetitionManagementController;
use App\Http\Controllers\Api\V1\Admin\EditionManagementController;
use App\Http\Controllers\Api\V1\Admin\PageVisitAnalyticsController;
use App\Http\Controllers\Api\V1\CompetitionController;
use App\Http\Controllers\Api\V1\EditionController;
use App\Http\Controllers\Api\V1\PageVisitController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\RegistratieController;
use App\Http\Controllers\Api\V1\RobotVoteController;
use App\Http\Controllers\Api\V1\RichMediaController;
use App\Http\Controllers\Api\V1\TeamRegistrationUpdateController;
use App\Http\Controllers\Api\V1\TeamRegistrationEditController;
use App\Http\Controllers\Api\V1\TeamMembershipController;
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

    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword'])
        ->middleware('throttle:6,1')
        ->name('auth.forgot-password');

    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:6,1')
        ->name('auth.reset-password');

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
    Route::get('/edities/{edition}/competitie', [CompetitionController::class, 'leaderboard'])
        ->name('competition.leaderboard');

    // CMS pages (public, read-only)
    Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');
    Route::post('/analytics/page-visits', [PageVisitController::class, 'store'])
        ->middleware('throttle:120,1')
        ->name('analytics.page-visits.store');

    // Team registration (authenticated, write)
    Route::post('/registratie', [RegistratieController::class, 'store'])
        ->middleware(['auth:sanctum', 'throttle:registratie'])
        ->name('registratie.store');

    // Authenticated user API
    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::patch('/auth/account', [AuthController::class, 'updateAccount'])->name('auth.account.update');
        Route::patch('/auth/password', [AuthController::class, 'updatePassword'])->name('auth.password.update');

        Route::get('/registratie/mijn', [TeamRegistrationEditController::class, 'show'])
            ->middleware(['throttle:registratie'])
            ->name('registratie.mine.show');

        Route::put('/registratie/mijn', [TeamRegistrationEditController::class, 'update'])
            ->middleware(['throttle:registratie'])
            ->name('registratie.mine.update');

        Route::get('/registratie/mijn/updates', [TeamRegistrationUpdateController::class, 'index'])
            ->middleware(['throttle:registratie'])
            ->name('registratie.mine.updates.index');

        Route::post('/registratie/mijn/updates', [TeamRegistrationUpdateController::class, 'store'])
            ->middleware(['throttle:registratie'])
            ->name('registratie.mine.updates.store');

        Route::patch('/registratie/mijn/updates/{teamUpdate}', [TeamRegistrationUpdateController::class, 'update'])
            ->middleware(['throttle:registratie'])
            ->name('registratie.mine.updates.update');

        Route::post('/teams/{team}/membership-requests', [TeamMembershipController::class, 'apply'])
            ->name('teams.membership.apply');

        Route::post('/robots/{robot}/vote', [RobotVoteController::class, 'store'])
            ->name('robots.vote.store');

        Route::get('/teams/mijn/lidmaatschappen', [TeamMembershipController::class, 'myMemberships'])
            ->name('teams.membership.mine.index');

        Route::get('/teams/mijn/membership-requests', [TeamMembershipController::class, 'captainRequests'])
            ->middleware('role:teamcaptain,moderator,admin')
            ->name('teams.membership.captain.index');

        Route::patch('/teams/mijn/membership-requests/{teamMembership}', [TeamMembershipController::class, 'review'])
            ->middleware('role:teamcaptain,moderator,admin')
            ->name('teams.membership.captain.review');

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

            Route::get('/robots', [RobotManagementController::class, 'index'])
                ->name('robots.index');
            Route::post('/robots', [RobotManagementController::class, 'store'])
                ->name('robots.store');
            Route::patch('/robots/{robot}', [RobotManagementController::class, 'update'])
                ->name('robots.update');
            Route::delete('/robots/{robot}', [RobotManagementController::class, 'destroy'])
                ->name('robots.destroy');

            Route::get('/links', [LinkManagementController::class, 'index'])
                ->name('links.index');
            Route::post('/links', [LinkManagementController::class, 'store'])
                ->name('links.store');
            Route::patch('/links/{link}', [LinkManagementController::class, 'update'])
                ->name('links.update');
            Route::delete('/links/{link}', [LinkManagementController::class, 'destroy'])
                ->name('links.destroy');

            Route::get('/locations', [LocationManagementController::class, 'index'])
                ->name('locations.index');

            Route::get('/edities', [EditionManagementController::class, 'index'])
                ->name('editions.index');
            Route::post('/edities', [EditionManagementController::class, 'store'])
                ->name('editions.store');
            Route::patch('/edities/{edition}', [EditionManagementController::class, 'update'])
                ->name('editions.update');
            Route::delete('/edities/{edition}', [EditionManagementController::class, 'destroy'])
                ->name('editions.destroy');

            Route::get('/edities/{edition}/competitie', [CompetitionManagementController::class, 'index'])
                ->name('competition.index');
            Route::post('/edities/{edition}/competitie/categories', [CompetitionManagementController::class, 'storeCategory'])
                ->name('competition.categories.store');
            Route::patch('/competitie/categories/{competitionCategory}', [CompetitionManagementController::class, 'updateCategory'])
                ->name('competition.categories.update');
            Route::post('/competitie/categories/{competitionCategory}/battles', [CompetitionManagementController::class, 'storeBattle'])
                ->name('competition.battles.store');
            Route::patch('/competitie/battles/{competitionBattle}', [CompetitionManagementController::class, 'updateBattle'])
                ->name('competition.battles.update');
            Route::put('/competitie/battles/{competitionBattle}/scores', [CompetitionManagementController::class, 'upsertScores'])
                ->name('competition.battles.scores.upsert');

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

            Route::prefix('analytics')->name('analytics.')->middleware('role:admin')->group(function (): void {
                Route::get('/page-visits', [PageVisitAnalyticsController::class, 'index'])->name('page-visits');
            });
        });
    });
});
