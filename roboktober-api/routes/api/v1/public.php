<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AnalyticsEventController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CompetitionController;
use App\Http\Controllers\Api\V1\EditionController;
use App\Http\Controllers\Api\V1\LinkController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\PageVisitController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\ProgrammaController;
use App\Http\Controllers\Api\V1\RegistratieController;
use App\Http\Controllers\Api\V1\TeamController;
use Illuminate\Support\Facades\Route;

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
Route::get('/edities/{edition}/programma', [ProgrammaController::class, 'index'])->name('editions.programma.index');
Route::get('/edities/{edition}/competitie', [CompetitionController::class, 'leaderboard'])
    ->name('competition.leaderboard');

// CMS pages (public, read-only)
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');
Route::post('/analytics/page-visits', [PageVisitController::class, 'store'])
    ->middleware('throttle:120,1')
    ->name('analytics.page-visits.store');

Route::post('/analytics/events', [AnalyticsEventController::class, 'store'])
    ->middleware('throttle:240,1')
    ->name('analytics.events.store');

// Team registration (authenticated, write)
Route::post('/registratie', [RegistratieController::class, 'store'])
    ->middleware(['auth:sanctum', 'throttle:registratie'])
    ->name('registratie.store');
