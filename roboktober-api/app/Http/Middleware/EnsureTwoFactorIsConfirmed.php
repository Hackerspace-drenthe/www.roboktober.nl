<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorIsConfirmed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User || $user->two_factor_confirmed_at !== null) {
            return $next($request);
        }

        $token = $user->currentAccessToken();
        $tokenName = $token->getAttribute('name');

        if (! is_string($tokenName) || $tokenName !== '2fa-bootstrap') {
            return $next($request);
        }

        $routeName = $request->route()->getName();

        if (is_string($routeName) && in_array($routeName, [
            'api.v1.auth.me',
            'api.v1.auth.logout',
            'api.v1.auth.2fa.setup',
            'api.v1.auth.2fa.confirm',
        ], true)) {
            return $next($request);
        }

        return new JsonResponse([
            'message' => 'Twee-factor-authenticatie moet eerst worden ingesteld.',
            'code' => 'two_factor_setup_required',
        ], Response::HTTP_FORBIDDEN);
    }
}
