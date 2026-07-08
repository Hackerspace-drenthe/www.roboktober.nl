<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Team;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureValidRegistrationEditToken
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = (string) $request->route('token', '');

        if ($token === '') {
            abort(404);
        }

        $tokenHash = hash('sha256', $token);

        $exists = Team::query()
            ->where('edit_token_hash', $tokenHash)
            ->whereNotNull('edit_token_expires_at')
            ->where('edit_token_expires_at', '>', now())
            ->exists();

        if (! $exists) {
            abort(404);
        }

        return $next($request);
    }
}
