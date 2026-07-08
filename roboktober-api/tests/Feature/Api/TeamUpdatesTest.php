<?php

declare(strict_types=1);

use App\Enums\TeamStatus;
use App\Enums\UserRole;
use App\Models\Edition;
use App\Models\Team;
use App\Models\TeamUpdate;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function maakTokenVoorTeamUpdates(Team $team): string
{
    $token = bin2hex(random_bytes(32));

    $team->forceFill([
        'edit_token_hash' => hash('sha256', $token),
        'edit_token_expires_at' => now()->addDays(30),
    ])->save();

    return $token;
}

describe('Team voortgangsupdates', function (): void {
    beforeEach(function (): void {
        $this->edition = Edition::factory()->create([
            'is_done' => false,
        ]);

        $this->team = Team::factory()->create([
            'edition_id' => $this->edition->id,
            'status' => TeamStatus::Approved,
        ]);

        $this->captain = User::factory()->create([
            'email' => $this->team->email,
            'role' => UserRole::TeamCaptain,
        ]);

        $this->team->forceFill([
            'captain_user_id' => $this->captain->id,
        ])->save();

        $this->token = maakTokenVoorTeamUpdates($this->team);
    });

    it('allows posting a team progress update via edit token', function (): void {
        Storage::fake('public');
        $accessToken = $this->captain->createToken('pest-updates')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->post('/api/v1/registratie/'.$this->token.'/updates', [
                'titel' => 'Eerste build-update',
                'excerpt' => 'Nieuwe armor geplaatst.',
                'content' => '<p>We hebben de robot aangepast.</p>',
                'content_format' => 'html',
                'afbeeldingen' => [
                    UploadedFile::fake()->create('update-1.jpg', 128, 'image/jpeg'),
                ],
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.titel', 'Eerste build-update')
            ->assertJsonPath('data.content_format', 'html');

        $this->assertDatabaseHas('team_updates', [
            'team_id' => $this->team->id,
            'titel' => 'Eerste build-update',
            'is_published' => true,
        ]);

        $update = TeamUpdate::query()->where('team_id', $this->team->id)->firstOrFail();
        expect($update->mediaCollectie('gallery')->count())->toBe(1);
    });

    it('returns team updates for a valid edit token', function (): void {
        TeamUpdate::query()->create([
            'team_id' => $this->team->id,
            'titel' => 'Update A',
            'content' => 'Voortgang A',
            'content_format' => 'markdown',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/registratie/'.$this->token.'/updates');

        $response->assertOk()
            ->assertJsonPath('data.0.titel', 'Update A');
    });

    it('blocks unauthenticated update posting', function (): void {
        $this->postJson('/api/v1/registratie/'.$this->token.'/updates', [
            'titel' => 'Niet toegestaan',
            'content' => 'Anoniem posten',
            'content_format' => 'markdown',
        ])->assertUnauthorized();
    });

    it('shows only published updates on the public team endpoint', function (): void {
        TeamUpdate::query()->create([
            'team_id' => $this->team->id,
            'titel' => 'Publieke update',
            'content' => 'Deze mag zichtbaar zijn.',
            'content_format' => 'markdown',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        TeamUpdate::query()->create([
            'team_id' => $this->team->id,
            'titel' => 'Interne update',
            'content' => 'Deze mag niet zichtbaar zijn.',
            'content_format' => 'markdown',
            'is_published' => false,
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/teams/'.$this->team->id);

        $response->assertOk()
            ->assertJsonFragment(['titel' => 'Publieke update'])
            ->assertJsonMissing(['titel' => 'Interne update']);
    });
});
