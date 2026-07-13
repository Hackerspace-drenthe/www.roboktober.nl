<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;
use App\Security\TotpService;
use Illuminate\Support\Facades\Password;

describe('API auth', function (): void {
    it('registers a user and returns bootstrap token for mandatory 2fa setup', function (): void {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Nieuwe Gebruiker',
            'email' => 'nieuw@example.test',
            'password' => 'SterkWachtwoord123',
            'password_confirmation' => 'SterkWachtwoord123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.email', 'nieuw@example.test')
            ->assertJsonPath('data.role', 'visitor')
            ->assertJsonPath('two_factor_setup_required', true)
            ->assertJsonStructure(['token', 'token_type', 'two_factor_provisioning' => ['secret']]);

        $this->assertDatabaseHas('users', [
            'email' => 'nieuw@example.test',
            'role' => UserRole::Visitor->value,
        ]);
    });

    it('requires 2fa challenge before issuing login token', function (): void {
        $user = User::factory()->create([
            'email' => 'captain@example.test',
            'password' => 'SterkWachtwoord123',
            'role' => UserRole::TeamCaptain,
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
            'two_factor_confirmed_at' => now(),
        ]);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'SterkWachtwoord123',
            'device_name' => 'pest-suite',
        ]);

        $login->assertOk()
            ->assertJsonPath('data.role', UserRole::TeamCaptain->value)
            ->assertJsonPath('two_factor_required', true)
            ->assertJsonPath('token', null);

        $challengeId = (string) $login->json('two_factor_challenge_id');
        $code = app(TotpService::class)->currentCode('JBSWY3DPEHPK3PXP');

        $challenge = $this->postJson('/api/v1/auth/2fa/challenge', [
            'challenge_id' => $challengeId,
            'code' => $code,
            'device_name' => 'pest-suite',
        ]);

        $token = (string) $challenge->json('token');

        $challenge->assertOk()
            ->assertJsonPath('data.role', UserRole::TeamCaptain->value)
            ->assertJsonStructure(['token', 'token_type']);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonPath('data.role', UserRole::TeamCaptain->value);
    });

    it('returns 2fa setup required for users without confirmed 2fa', function (): void {
        $user = User::factory()->create([
            'email' => 'setup@example.test',
            'password' => 'SterkWachtwoord123',
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'SterkWachtwoord123',
        ]);

        $response->assertOk()
            ->assertJsonPath('two_factor_setup_required', true)
            ->assertJsonStructure(['token', 'two_factor_provisioning' => ['secret']]);
    });

    it('confirms 2fa setup and issues a full api token', function (): void {
        $register = $this->postJson('/api/v1/auth/register', [
            'name' => '2FA User',
            'email' => '2fa@example.test',
            'password' => 'SterkWachtwoord123',
            'password_confirmation' => 'SterkWachtwoord123',
        ])->assertCreated();

        $bootstrapToken = (string) $register->json('token');
        $secret = (string) $register->json('two_factor_provisioning.secret');
        $code = app(TotpService::class)->currentCode($secret);

        $confirm = $this->withHeader('Authorization', 'Bearer '.$bootstrapToken)
            ->postJson('/api/v1/auth/2fa/confirm', [
                'code' => $code,
                'device_name' => 'pest-suite',
            ]);

        $confirm->assertOk()
            ->assertJsonPath('data.two_factor_enabled', true)
            ->assertJsonStructure(['token', 'token_type']);

        $this->assertDatabaseHas('users', [
            'email' => '2fa@example.test',
        ]);
    });

    it('updates account name and email', function (): void {
        $user = User::factory()->create([
            'name' => 'Oude Naam',
            'email' => 'oud@example.test',
            'role' => UserRole::TeamCaptain,
            'password' => 'SterkWachtwoord123',
        ]);

        $accessToken = $user->createToken('pest-auth')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->patchJson('/api/v1/auth/account', [
                'name' => 'Nieuwe Naam',
                'email' => 'nieuw@example.test',
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Nieuwe Naam')
            ->assertJsonPath('data.email', 'nieuw@example.test');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => UserRole::TeamCaptain->value,
            'name' => 'Nieuwe Naam',
            'email' => 'nieuw@example.test',
        ]);
    });

    it('updates password for authenticated user', function (): void {
        $user = User::factory()->create([
            'email' => 'pass@example.test',
            'role' => UserRole::TeamCaptain,
            'password' => 'OudSterkWachtwoord123',
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
            'two_factor_confirmed_at' => now(),
        ]);

        $accessToken = $user->createToken('pest-auth')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->patchJson('/api/v1/auth/password', [
                'current_password' => 'OudSterkWachtwoord123',
                'password' => 'NieuwSterkWachtwoord123',
                'password_confirmation' => 'NieuwSterkWachtwoord123',
            ])
            ->assertOk();

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'pass@example.test',
            'password' => 'NieuwSterkWachtwoord123',
            'device_name' => 'pest-suite',
        ])->assertOk();

        $this->postJson('/api/v1/auth/2fa/challenge', [
            'challenge_id' => (string) $login->json('two_factor_challenge_id'),
            'code' => app(TotpService::class)->currentCode('JBSWY3DPEHPK3PXP'),
            'device_name' => 'pest-suite',
        ])->assertOk();
    });

    it('sends forgot password response and resets password with token', function (): void {
        $user = User::factory()->create([
            'email' => 'reset@example.test',
            'password' => 'OudResetWachtwoord123',
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
            'two_factor_confirmed_at' => now(),
        ]);

        $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'reset@example.test',
        ])->assertOk();

        $token = Password::broker()->createToken($user);

        $this->postJson('/api/v1/auth/reset-password', [
            'email' => 'reset@example.test',
            'token' => $token,
            'password' => 'NieuwResetWachtwoord123',
            'password_confirmation' => 'NieuwResetWachtwoord123',
        ])->assertOk();

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'reset@example.test',
            'password' => 'NieuwResetWachtwoord123',
            'device_name' => 'pest-suite',
        ])->assertOk();

        $this->postJson('/api/v1/auth/2fa/challenge', [
            'challenge_id' => (string) $login->json('two_factor_challenge_id'),
            'code' => app(TotpService::class)->currentCode('JBSWY3DPEHPK3PXP'),
            'device_name' => 'pest-suite',
        ])->assertOk();
    });
});
