<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\LoginUserRequest;
use App\Http\Requests\Api\V1\RegisterUserRequest;
use App\Http\Requests\Api\V1\ResetPasswordRequest;
use App\Http\Requests\Api\V1\UpdateAccountRequest;
use App\Http\Requests\Api\V1\UpdatePasswordRequest;
use App\Http\Resources\Api\V1\AuthUserResource;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
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

        $user->load('media');

        return new AuthUserResource($user);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Uitgelogd.',
        ], Response::HTTP_OK);
    }

    public function updateAccount(UpdateAccountRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        /** @var array{name: string, email: string} $validated */
        $validated = $request->validated();

        $user->forceFill([
            'name' => $validated['name'],
            'email' => mb_strtolower($validated['email']),
        ])->save();

        $user->load('media');

        return response()->json([
            'message' => 'Account bijgewerkt.',
            'data' => new AuthUserResource(($user->fresh() ?? $user)->load('media')),
        ], Response::HTTP_OK);
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        /** @var array{current_password: string, password: string, password_confirmation: string} $validated */
        $validated = $request->validated();

        $user->forceFill([
            'password' => $validated['password'],
        ])->save();

        return response()->json([
            'message' => 'Wachtwoord bijgewerkt.',
        ], Response::HTTP_OK);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        /** @var array{email: string} $validated */
        $validated = $request->validated();

        ResetPasswordNotification::createUrlUsing(static function (mixed $user, string $token): string {
            $configuredAppUrl = config('app.url');
            $baseUrl = rtrim(is_string($configuredAppUrl) ? $configuredAppUrl : '', '/');
            $email = $user instanceof User ? $user->email : '';

            return $baseUrl.'/app/wachtwoord-reset?token='.$token.'&email='.urlencode($email);
        });

        Password::sendResetLink([
            'email' => mb_strtolower($validated['email']),
        ]);

        return response()->json([
            'message' => 'Als dit e-mailadres bekend is, is een resetlink verstuurd.',
        ], Response::HTTP_OK);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        /** @var array{email: string, token: string, password: string, password_confirmation: string} $validated */
        $validated = $request->validated();

        $status = Password::reset(
            [
                'email' => mb_strtolower($validated['email']),
                'password' => $validated['password'],
                'password_confirmation' => $validated['password_confirmation'],
                'token' => $validated['token'],
            ],
            static function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Resetten van wachtwoord is mislukt.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            'message' => 'Wachtwoord is opnieuw ingesteld.',
        ], Response::HTTP_OK);
    }
}
