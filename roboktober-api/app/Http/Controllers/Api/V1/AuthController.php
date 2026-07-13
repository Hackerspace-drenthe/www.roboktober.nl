<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CompleteTwoFactorChallengeRequest;
use App\Http\Requests\Api\V1\ConfirmTwoFactorSetupRequest;
use App\Http\Requests\Api\V1\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\LoginUserRequest;
use App\Http\Requests\Api\V1\RegisterUserRequest;
use App\Http\Requests\Api\V1\ResetPasswordRequest;
use App\Http\Requests\Api\V1\UpdateAccountRequest;
use App\Http\Requests\Api\V1\UpdatePasswordRequest;
use App\Http\Resources\Api\V1\AuthUserResource;
use App\Models\User;
use App\Security\TotpService;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(private readonly TotpService $totpService) {}

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

        $setupPayload = $this->buildTwoFactorSetupPayload($user);
        $token = $this->issueApiToken($user, '2fa-bootstrap');

        return response()->json([
            'data' => new AuthUserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
            'two_factor_setup_required' => true,
            'two_factor_provisioning' => $setupPayload,
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

        if ($user->two_factor_confirmed_at === null) {
            $token = $this->issueApiToken($user, '2fa-bootstrap');

            return response()->json([
                'data' => new AuthUserResource($user),
                'token' => $token,
                'token_type' => 'Bearer',
                'two_factor_setup_required' => true,
                'two_factor_provisioning' => $this->buildTwoFactorSetupPayload($user),
            ], Response::HTTP_OK);
        }

        $challengeId = (string) Str::uuid();
        Cache::put($this->challengeCacheKey($challengeId), $user->id, now()->addMinutes(5));

        return response()->json([
            'data' => new AuthUserResource($user),
            'token' => null,
            'token_type' => 'Bearer',
            'two_factor_required' => true,
            'two_factor_challenge_id' => $challengeId,
        ], Response::HTTP_OK);
    }

    public function completeTwoFactorChallenge(CompleteTwoFactorChallengeRequest $request): JsonResponse
    {
        /** @var array{challenge_id: string, code: string, device_name?: string} $validated */
        $validated = $request->validated();

        $cacheKey = $this->challengeCacheKey($validated['challenge_id']);
        $userId = Cache::pull($cacheKey);

        if (! is_int($userId)) {
            return response()->json([
                'message' => 'Twee-factor challenge is verlopen of ongeldig.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::query()->find($userId);

        if (! $user instanceof User || ! is_string($user->two_factor_secret) || $user->two_factor_secret === '') {
            return response()->json([
                'message' => 'Twee-factor configuratie ontbreekt.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (! $this->totpService->verifyCode($user->two_factor_secret, $validated['code'])) {
            return response()->json([
                'message' => 'Ongeldige twee-factor code.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $token = $this->issueApiToken($user, $validated['device_name'] ?? 'api-login');

        return response()->json([
            'data' => new AuthUserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], Response::HTTP_OK);
    }

    public function twoFactorSetup(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'data' => $this->buildTwoFactorSetupPayload($user),
        ], Response::HTTP_OK);
    }

    public function confirmTwoFactorSetup(ConfirmTwoFactorSetupRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        /** @var array{code: string, device_name?: string} $validated */
        $validated = $request->validated();

        $secret = $this->ensureTwoFactorSecret($user);

        if (! $this->totpService->verifyCode($secret, $validated['code'])) {
            return response()->json([
                'message' => 'Ongeldige twee-factor code.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
        ])->save();

        $user->currentAccessToken()->delete();

        $token = $this->issueApiToken($user, $validated['device_name'] ?? 'api-login');

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

    private function issueApiToken(User $user, string $name): string
    {
        return $user->createToken(
            $name,
            $user->tokenAbilities(),
            now()->addDays(30),
        )->plainTextToken;
    }

    /**
     * @return array{secret: string, otpauth_url: string, issuer: string, account: string}
     */
    private function buildTwoFactorSetupPayload(User $user): array
    {
        $secret = $this->ensureTwoFactorSecret($user);
        $configuredName = config('app.name');
        $issuer = is_string($configuredName) && $configuredName !== '' ? $configuredName : 'Roboktober';

        return [
            'secret' => $secret,
            'otpauth_url' => $this->totpService->otpauthUrl($issuer, $user->email, $secret),
            'issuer' => $issuer,
            'account' => $user->email,
        ];
    }

    private function ensureTwoFactorSecret(User $user): string
    {
        if (is_string($user->two_factor_secret) && $user->two_factor_secret !== '') {
            return $user->two_factor_secret;
        }

        $secret = $this->totpService->generateSecret();

        $user->forceFill([
            'two_factor_secret' => $secret,
        ])->save();

        return $secret;
    }

    private function challengeCacheKey(string $challengeId): string
    {
        return 'auth:2fa:challenge:'.$challengeId;
    }
}
