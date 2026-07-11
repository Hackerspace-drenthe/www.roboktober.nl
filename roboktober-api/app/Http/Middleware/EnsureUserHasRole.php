<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $actor = $request->user();

        if (! $actor instanceof User) {
            abort(401, 'Niet ingelogd.');
        }

        $allowedRoles = array_map(
            static fn (string $role): UserRole => UserRole::from(trim($role)),
            $roles,
        );

        if (! $actor->hasAnyRole(...$allowedRoles)) {
            abort(403, 'Geen toegang voor deze rol.');
        }

        return $next($request);
    }
}
