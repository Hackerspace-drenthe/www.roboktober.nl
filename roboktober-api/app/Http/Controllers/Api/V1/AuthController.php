<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ClaimTeamRequest;
use App\Http\Requests\Api\V1\LoginUserRequest;
use App\Http\Requests\Api\V1\RegisterUserRequest;
use App\Http\Resources\Api\V1\AuthUserResource;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request): JsonResponse
    {
        /** @var array{name: string, email: string, password: string} $validated */
        $validated = $request->validated();

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => mb_strtolower($validated['email']),
            'password' => $validated['password'],
            'role' => UserRole::Visitor,
        ]);

        $token = $user->createToken('api-register', $user->tokenAbilities(), now()->addDays(30))->plainTextToken;

        return response()->json([
            'data' => new AuthUserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], Response::HTTP_CREATED);
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        /** @var array{email: string, password: string, device_name?: string} $validated */
        $validated = $request->validated();

        $user = User::query()->where('email', mb_strtolower($validated['email']))->first();

        if (! $user instanceof User || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Ongeldige inloggegevens.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $token = $user->createToken(
            $validated['device_name'] ?? 'api-login',
            $user->tokenAbilities(),
            now()->addDays(30),
        )->plainTextToken;

        return response()->json([
            'data' => new AuthUserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], Response::HTTP_OK);
    }

    public function me(Request $request): AuthUserResource
    {
        /** @var User $user */
        $user = $request->user();

        return new AuthUserResource($user);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Uitgelogd.',
        ], Response::HTTP_OK);
    }

    public function claimTeam(ClaimTeamRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        /** @var array{edit_token: string} $validated */
        $validated = $request->validated();

        $team = Team::query()
            ->where('edit_token_hash', hash('sha256', $validated['edit_token']))
            ->whereNotNull('edit_token_expires_at')
            ->where('edit_token_expires_at', '>', now())
            ->first();

        if (! $team instanceof Team) {
            return response()->json([
                'message' => 'Geen geldig team gevonden voor deze bewerkcode.',
            ], Response::HTTP_NOT_FOUND);
        }

        if (mb_strtolower($team->email) !== mb_strtolower($user->email)
            && ! $user->hasAnyRole(UserRole::Admin, UserRole::Moderator)) {
            return response()->json([
                'message' => 'Dit team hoort bij een ander e-mailadres.',
            ], Response::HTTP_FORBIDDEN);
        }

        if ($team->captain_user_id !== null && $team->captain_user_id !== $user->id) {
            return response()->json([
                'message' => 'Dit team is al gekoppeld aan een andere gebruiker.',
            ], Response::HTTP_CONFLICT);
        }

        $team->forceFill([
            'captain_user_id' => $user->id,
        ])->save();

        $user->promoteToTeamCaptainIfVisitor();

        return response()->json([
            'message' => 'Team succesvol gekoppeld aan jouw account.',
            'data' => [
                'team_id' => $team->id,
                'captain_user_id' => $team->captain_user_id,
                'user_role' => $user->fresh()?->role->value,
            ],
        ], Response::HTTP_OK);
    }

    public function teamEditLink(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $team = Team::query()
            ->where('captain_user_id', $user->id)
            ->first();

        if (! $team instanceof Team) {
            return response()->json([
                'message' => 'Geen team gevonden voor deze gebruiker. Koppel eerst je team via de bewerkcode.',
            ], HttpResponse::HTTP_NOT_FOUND);
        }

        $token = bin2hex(random_bytes(32));

        $team->forceFill([
            'edit_token_hash' => hash('sha256', $token),
            'edit_token_expires_at' => now()->addDays(30),
        ])->save();

        return response()->json([
            'message' => 'Nieuwe bewerklink uitgegeven.',
            'data' => [
                'edit_url' => $this->buildTeamEditUrl($token),
                'edit_token_expires_at' => $team->fresh()?->edit_token_expires_at?->toIso8601String(),
            ],
        ], Response::HTTP_OK);
    }

    private function buildTeamEditUrl(string $token): string
    {
        $baseUrl = rtrim((string) config('app.url'), '/');

        return $baseUrl.'/app/aanmelding/bewerken/'.$token;
    }
}
