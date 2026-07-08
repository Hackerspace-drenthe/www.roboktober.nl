<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('serves public app routes read-only via web layer', function (): void {
    $publicWebRoutes = collect(Route::getRoutes()->getRoutes())
        ->filter(function ($route): bool {
            $uri = trim($route->uri(), '/');

            return $uri === '' || str_starts_with($uri, 'app');
        });

    expect($publicWebRoutes)->not->toBeEmpty();

    $invalidMethods = $publicWebRoutes
        ->mapWithKeys(fn ($route): array => [$route->uri() => array_values(array_diff($route->methods(), ['GET', 'HEAD']))])
        ->filter(fn (array $methods): bool => count($methods) > 0)
        ->all();

    expect($invalidMethods)->toBe([]);
});

it('exposes user registration mutations only on api v1 routes', function (): void {
    $allRoutes = collect(Route::getRoutes()->getRoutes());

    $apiMutations = $allRoutes
        ->filter(function ($route): bool {
            $methods = array_diff($route->methods(), ['GET', 'HEAD', 'OPTIONS']);

            return count($methods) > 0 && str_starts_with($route->uri(), 'api/v1/');
        })
        ->map(fn ($route): string => $route->uri())
        ->values()
        ->all();

    expect($apiMutations)->toContain('api/v1/registratie');
    expect($apiMutations)->toContain('api/v1/registratie/{token}');

    $webRegistrationMutations = $allRoutes
        ->filter(function ($route): bool {
            $methods = array_diff($route->methods(), ['GET', 'HEAD', 'OPTIONS']);
            $uri = $route->uri();

            return count($methods) > 0
                && ! str_starts_with($uri, 'api/')
                && (str_contains($uri, 'registratie') || str_contains($uri, 'aanmeld'));
        })
        ->map(fn ($route): string => $route->uri())
        ->values()
        ->all();

    expect($webRegistrationMutations)->toBe([]);
});
