<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Admin\AdminDashboardController;
use App\Http\Controllers\Api\V1\Admin\AuditLogController;
use App\Http\Controllers\Api\V1\Admin\CompetitionManagementController;
use App\Http\Controllers\Api\V1\Admin\EditionManagementController;
use App\Http\Controllers\Api\V1\Admin\LinkManagementController;
use App\Http\Controllers\Api\V1\Admin\LocationManagementController;
use App\Http\Controllers\Api\V1\Admin\PageModerationController;
use App\Http\Controllers\Api\V1\Admin\PageVisitAnalyticsController;
use App\Http\Controllers\Api\V1\Admin\PostModerationController;
use App\Http\Controllers\Api\V1\Admin\ProgrammaItemManagementController;
use App\Http\Controllers\Api\V1\Admin\RobotManagementController;
use App\Http\Controllers\Api\V1\Admin\TeamModerationController;
use App\Http\Controllers\Api\V1\Admin\TeamUpdateModerationController;
use App\Http\Controllers\Api\V1\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

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
Route::get('/edities/{edition}/programma', [ProgrammaItemManagementController::class, 'index'])
    ->name('programma.index');
Route::post('/edities/{edition}/programma', [ProgrammaItemManagementController::class, 'store'])
    ->name('programma.store');
Route::patch('/programma/{programmaItem}', [ProgrammaItemManagementController::class, 'update'])
    ->name('programma.update');
Route::delete('/programma/{programmaItem}', [ProgrammaItemManagementController::class, 'destroy'])
    ->name('programma.destroy');
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
