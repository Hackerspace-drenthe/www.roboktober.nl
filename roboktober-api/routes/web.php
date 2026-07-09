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

// Support direct frontend URLs (e.g. /aanmelden) by redirecting them to /app/*.
Route::get('/{any}', function (string $any) {
    $incomingPath = request()->path();
    $basePath = request()->getBasePath();

    // Some local server setups route /app/* through this fallback; serve SPA directly to avoid /app/app/* redirects.
    if ($basePath === '/app' || str_starts_with($incomingPath, 'app/')) {
        $spaIndex = public_path('app/index.html');
        abort_unless(file_exists($spaIndex), 404);

        return response()->file($spaIndex);
    }

    $target = '/app/'.$any;
    $query = request()->getQueryString();

    if (is_string($query) && $query !== '') {
        $target .= '?'.$query;
    }

    return redirect($target);
})->where('any', '^(?!api|app|admin|storage|build|vendor|livewire|sanctum).*$');
