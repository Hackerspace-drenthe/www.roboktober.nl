<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \App\Http\Middleware\ApiSecurityHeaders::class,
        ]);

        $middleware->alias([
            'registration.edit-token' => \App\Http\Middleware\EnsureValidRegistrationEditToken::class,
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->is('livewire/*'),
        );

        $exceptions->render(function (InvalidSignatureException $exception, Request $request) {
            if (! $request->is('livewire/*')) {
                return null;
            }

            return response()->json([
                'message' => 'Livewire upload signature is ongeldig of verlopen. Vernieuw de pagina en probeer opnieuw.',
            ], 403);
        });

        $exceptions->render(function (PostTooLargeException $exception, Request $request) {
            if (! $request->is('livewire/*')) {
                return null;
            }

            return response()->json([
                'message' => 'Bestand te groot voor upload. Verlaag de bestandsgrootte of verhoog server upload limieten.',
            ], 413);
        });
    })->create();
