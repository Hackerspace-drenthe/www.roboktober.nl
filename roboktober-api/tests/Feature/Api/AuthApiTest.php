<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Password;

describe('API auth', function (): void {
    it('registers a user and returns bearer token', function (): void {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Nieuwe Gebruiker',
            'email' => 'nieuw@example.test',
            'password' => 'SterkWachtwoord123',
            'password_confirmation' => 'SterkWachtwoord123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.email', 'nieuw@example.test')
            ->assertJsonPath('data.role', 'visitor')
            ->assertJsonStructure(['token', 'token_type']);

        $this->assertDatabaseHas('users', [
            'email' => 'nieuw@example.test',
            'role' => UserRole::Visitor->value,
        ]);
    });

    it('allows login and returns current user data', function (): void {
        $user = User::factory()->create([
            'email' => 'captain@example.test',
            'password' => 'SterkWachtwoord123',
            'role' => UserRole::TeamCaptain,
        ]);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'SterkWachtwoord123',
            'device_name' => 'pest-suite',
        ]);

        $token = (string) $login->json('token');

        $login->assertOk()
            ->assertJsonPath('data.role', UserRole::TeamCaptain->value);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonPath('data.role', UserRole::TeamCaptain->value);
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
        ]);

        $accessToken = $user->createToken('pest-auth')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->patchJson('/api/v1/auth/password', [
                'current_password' => 'OudSterkWachtwoord123',
                'password' => 'NieuwSterkWachtwoord123',
                'password_confirmation' => 'NieuwSterkWachtwoord123',
            ])
            ->assertOk();

        $this->postJson('/api/v1/auth/login', [
            'email' => 'pass@example.test',
            'password' => 'NieuwSterkWachtwoord123',
            'device_name' => 'pest-suite',
        ])->assertOk();
    });

    it('sends forgot password response and resets password with token', function (): void {
        $user = User::factory()->create([
            'email' => 'reset@example.test',
            'password' => 'OudResetWachtwoord123',
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

        $this->postJson('/api/v1/auth/login', [
            'email' => 'reset@example.test',
            'password' => 'NieuwResetWachtwoord123',
            'device_name' => 'pest-suite',
        ])->assertOk();
    });
});
