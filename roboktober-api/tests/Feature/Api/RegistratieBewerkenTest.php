<?php

declare(strict_types=1);

use App\Enums\TeamStatus;
use App\Enums\UserRole;
use App\Models\Edition;
use App\Models\Robot;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function maakBewerkTokenVoorTeam(Team $team): string
{
    $token = bin2hex(random_bytes(32));

    $team->forceFill([
        'edit_token_hash' => hash('sha256', $token),
        'edit_token_expires_at' => now()->addDays(30),
    ])->save();

    return $token;
}

describe('Registratie bewerken via token', function (): void {
    beforeEach(function (): void {
        $this->edition = Edition::factory()->create([
            'naam' => 'Roboktober 2026',
            'is_done' => false,
        ]);

        $this->team = Team::factory()->create([
            'edition_id' => $this->edition->id,
            'naam' => 'Bestaand Team',
            'contactpersoon' => 'Oude Contact',
            'email' => 'oud@example.com',
            'volwassenen' => 2,
            'status' => TeamStatus::Pending,
        ]);

        $this->captain = User::factory()->create([
            'email' => 'oud@example.com',
            'role' => UserRole::TeamCaptain,
        ]);

        $this->team->forceFill([
            'captain_user_id' => $this->captain->id,
        ])->save();

        Robot::factory()->create([
            'team_id' => $this->team->id,
            'naam' => 'Oude Robot',
            'gewichtsklasse' => 'antweight',
        ]);

        $this->token = maakBewerkTokenVoorTeam($this->team);
    });

    it('returns editable registration data for valid token', function (): void {
        $response = $this->getJson('/api/v1/registratie/'.$this->token);

        $response->assertOk()
            ->assertJsonPath('data.naam', 'Bestaand Team')
            ->assertJsonPath('data.contactpersoon', 'Oude Contact')
            ->assertJsonPath('data.email', 'oud@example.com')
            ->assertJsonPath('data.robots.0.naam', 'Oude Robot');
    });

    it('updates team and robots for valid token', function (): void {
        $accessToken = $this->captain->createToken('pest-edit')->plainTextToken;

        $payload = [
            'edition_id' => $this->edition->id,
            'naam' => 'Nieuw Team',
            'contactpersoon' => 'Nieuw Contact',
            'email' => 'nieuw@example.com',
            'volwassenen' => 3,
            'robots' => [
                [
                    'naam' => 'Nieuwe Robot 1',
                    'gewichtsklasse' => 'beetleweight',
                    'beschrijving' => 'Aangepast model',
                ],
                [
                    'naam' => 'Nieuwe Robot 2',
                    'gewichtsklasse' => 'antweight',
                ],
            ],
        ];

        $response = $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->putJson('/api/v1/registratie/'.$this->token, $payload);

        $response->assertOk()
            ->assertJsonPath('data.naam', 'Nieuw Team')
            ->assertJsonFragment(['naam' => 'Nieuwe Robot 1'])
            ->assertJsonFragment(['naam' => 'Nieuwe Robot 2']);

        $this->assertDatabaseHas('teams', [
            'id' => $this->team->id,
            'naam' => 'Nieuw Team',
            'contactpersoon' => 'Nieuw Contact',
            'email' => 'nieuw@example.com',
            'volwassenen' => 3,
        ]);

        $this->assertDatabaseHas('robots', [
            'team_id' => $this->team->id,
            'naam' => 'Nieuwe Robot 1',
            'gewichtsklasse' => 'beetleweight',
        ]);

        $this->assertDatabaseMissing('robots', [
            'team_id' => $this->team->id,
            'naam' => 'Oude Robot',
        ]);
    });

    it('can replace team photo', function (): void {
        Storage::fake('public');
        $accessToken = $this->captain->createToken('pest-edit-photo')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->post('/api/v1/registratie/'.$this->token, [
                '_method' => 'PUT',
                'edition_id' => $this->edition->id,
                'naam' => 'Bestaand Team',
                'contactpersoon' => 'Oude Contact',
                'email' => 'oud@example.com',
                'volwassenen' => 2,
                'robots' => [
                    [
                        'naam' => 'Nieuwe Robot',
                        'gewichtsklasse' => 'antweight',
                    ],
                ],
                'teamfoto' => UploadedFile::fake()->create('nieuw-team.jpg', 128, 'image/jpeg'),
            ]);

        $response->assertOk();

        $team = Team::query()->findOrFail($this->team->id);
        expect($team->mediaCollectie('foto')->count())->toBe(1);
    });

    it('rejects expired token', function (): void {
        $accessToken = $this->captain->createToken('pest-expired')->plainTextToken;

        $this->team->forceFill([
            'edit_token_expires_at' => now()->subMinute(),
        ])->save();

        $this->getJson('/api/v1/registratie/'.$this->token)->assertNotFound();
        $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->putJson('/api/v1/registratie/'.$this->token, [])->assertNotFound();
    });

    it('blocks unauthenticated write access', function (): void {
        $this->putJson('/api/v1/registratie/'.$this->token, [])->assertUnauthorized();
    });
});
