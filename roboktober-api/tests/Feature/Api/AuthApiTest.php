<?php

declare(strict_types=1);

use App\Enums\TeamStatus;
use App\Enums\UserRole;
use App\Models\Team;
use App\Models\User;

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

    it('claims a team and promotes visitor to teamcaptain', function (): void {
        $user = User::factory()->create([
            'email' => 'team@example.test',
            'role' => UserRole::Visitor,
            'password' => 'SterkWachtwoord123',
        ]);

        $token = bin2hex(random_bytes(32));

        $team = Team::factory()->create([
            'email' => 'team@example.test',
            'status' => TeamStatus::Pending,
            'edit_token_hash' => hash('sha256', $token),
            'edit_token_expires_at' => now()->addDays(20),
            'captain_user_id' => null,
        ]);

        $accessToken = $user->createToken('pest-auth')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->postJson('/api/v1/auth/claim-team', [
                'edit_token' => $token,
            ])
            ->assertOk()
            ->assertJsonPath('data.team_id', $team->id)
            ->assertJsonPath('data.user_role', UserRole::TeamCaptain->value);

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'captain_user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => UserRole::TeamCaptain->value,
        ]);
    });

    it('issues a fresh team edit link for logged in captain', function (): void {
        $user = User::factory()->create([
            'email' => 'captain-link@example.test',
            'role' => UserRole::TeamCaptain,
        ]);

        Team::factory()->create([
            'captain_user_id' => $user->id,
            'email' => $user->email,
            'edit_token_hash' => null,
            'edit_token_expires_at' => null,
        ]);

        $accessToken = $user->createToken('pest-auth')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->postJson('/api/v1/auth/team-edit-link');

        $response->assertOk()
            ->assertJsonPath('message', 'Nieuwe bewerklink uitgegeven.');

        $url = (string) $response->json('data.edit_url');

        expect($url)->toStartWith(rtrim((string) config('app.url'), '/').'/app/aanmelding/bewerken/');

        $team = Team::query()->where('captain_user_id', $user->id)->firstOrFail();
        expect($team->edit_token_hash)->not->toBeNull();
        expect($team->edit_token_expires_at)->not->toBeNull();
    });

    it('returns 404 when user has no claimed team for edit link', function (): void {
        $user = User::factory()->create([
            'role' => UserRole::Visitor,
        ]);

        $accessToken = $user->createToken('pest-auth')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->postJson('/api/v1/auth/team-edit-link')
            ->assertNotFound()
            ->assertJsonPath('message', 'Geen team gevonden voor deze gebruiker. Koppel eerst je team via de bewerkcode.');
    });
});
