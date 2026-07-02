<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// Redirect root naar de Vue SPA
Route::get('/', fn () => redirect('/app/'));

// Serveer de Vue 3 SPA voor alle /app/* routes, maar NIET /app/admin/*
Route::get('/app/{any?}', function () {
    $path = public_path('app/index.html');

    abort_unless(file_exists($path), 404);

    return response()->file($path);
})->where('any', '^(?!admin).*$');
