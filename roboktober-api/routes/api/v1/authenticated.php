<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\RichMediaController;
use App\Http\Controllers\Api\V1\RobotVoteController;
use App\Http\Controllers\Api\V1\TeamMembershipController;
use App\Http\Controllers\Api\V1\TeamRegistrationEditController;
use App\Http\Controllers\Api\V1\TeamRegistrationUpdateController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('admin')->name('admin.')->middleware('role:moderator,admin')->group(function (): void {
    require __DIR__.'/admin.php';
});
