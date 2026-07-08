<?php

declare(strict_types=1);

use App\Enums\TeamStatus;
use App\Enums\UserRole;
use App\Models\Edition;
use App\Models\Robot;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;

describe('Registratie bewerken via account', function (): void {
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
    });

    it('returns editable registration data for authenticated captain', function (): void {
        $accessToken = $this->captain->createToken('pest-read')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->getJson('/api/v1/registratie/mijn');

        $response->assertOk()
            ->assertJsonPath('data.naam', 'Bestaand Team')
            ->assertJsonPath('data.contactpersoon', 'Oude Contact')
            ->assertJsonPath('data.email', 'oud@example.com')
            ->assertJsonPath('data.robots.0.naam', 'Oude Robot');
    });

    it('updates team and robots for authenticated captain', function (): void {
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
            ->putJson('/api/v1/registratie/mijn', $payload);

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

    it('allows editing own team even when status is rejected', function (): void {
        $this->team->forceFill([
            'status' => TeamStatus::Rejected,
        ])->save();

        $accessToken = $this->captain->createToken('pest-edit-rejected')->plainTextToken;

        $payload = [
            'edition_id' => $this->edition->id,
            'naam' => 'Rejected Maar Bewerkbaar',
            'contactpersoon' => 'Captain Rejected',
            'email' => 'rejected@example.com',
            'volwassenen' => 2,
            'robots' => [
                [
                    'naam' => 'Doorzetten',
                    'gewichtsklasse' => 'antweight',
                ],
            ],
        ];

        $response = $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->putJson('/api/v1/registratie/mijn', $payload);

        $response->assertOk()
            ->assertJsonPath('data.naam', 'Rejected Maar Bewerkbaar');

        $this->assertDatabaseHas('teams', [
            'id' => $this->team->id,
            'naam' => 'Rejected Maar Bewerkbaar',
            'status' => TeamStatus::Rejected->value,
            'captain_user_id' => $this->captain->id,
        ]);
    });

    it('can replace team photo', function (): void {
        Storage::fake('public');
        $accessToken = $this->captain->createToken('pest-edit-photo')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->post('/api/v1/registratie/mijn', [
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

    it('returns 404 when authenticated user has no team', function (): void {
        $andereUser = User::factory()->create([
            'role' => UserRole::Visitor,
        ]);

        $accessToken = $andereUser->createToken('pest-no-team')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->getJson('/api/v1/registratie/mijn')
            ->assertNotFound();
    });

    it('blocks unauthenticated write access', function (): void {
        $this->putJson('/api/v1/registratie/mijn', [])->assertUnauthorized();
    });

    it('does not quickly throttle repeated reads of own registration', function (): void {
        RateLimiter::clear('registratie-account-user:'.$this->captain->id);

        $accessToken = $this->captain->createToken('pest-read-burst')->plainTextToken;

        for ($i = 0; $i < 10; $i++) {
            $this->withHeader('Authorization', 'Bearer '.$accessToken)
                ->getJson('/api/v1/registratie/mijn')
                ->assertOk();
        }
    });
});
