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
    });

    it('allows posting a team progress update via account', function (): void {
        Storage::fake('public');
        $accessToken = $this->captain->createToken('pest-updates')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->post('/api/v1/registratie/mijn/updates', [
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

    it('allows posting own team update while team is pending', function (): void {
        $this->team->forceFill([
            'status' => TeamStatus::Pending,
        ])->save();

        $accessToken = $this->captain->createToken('pest-updates-pending')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->post('/api/v1/registratie/mijn/updates', [
                'titel' => 'Pending update',
                'content' => 'Nog in behandeling, wel voortgang.',
                'content_format' => 'markdown',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.titel', 'Pending update');

        $this->assertDatabaseHas('team_updates', [
            'team_id' => $this->team->id,
            'titel' => 'Pending update',
        ]);
    });

    it('returns team updates for authenticated captain', function (): void {
        $accessToken = $this->captain->createToken('pest-updates-read')->plainTextToken;

        TeamUpdate::query()->create([
            'team_id' => $this->team->id,
            'titel' => 'Update A',
            'content' => 'Voortgang A',
            'content_format' => 'markdown',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->getJson('/api/v1/registratie/mijn/updates');

        $response->assertOk()
            ->assertJsonPath('data.0.titel', 'Update A');
    });

    it('allows captain to edit own team update content', function (): void {
        $update = TeamUpdate::query()->create([
            'team_id' => $this->team->id,
            'titel' => 'Oude titel',
            'excerpt' => 'Oude intro',
            'content' => 'Oude inhoud',
            'content_format' => 'markdown',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $accessToken = $this->captain->createToken('pest-updates-edit')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->patch('/api/v1/registratie/mijn/updates/'.$update->id, [
                'titel' => 'Nieuwe titel',
                'excerpt' => 'Nieuwe intro',
                'content' => 'Nieuwe inhoud',
                'content_format' => 'html',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.titel', 'Nieuwe titel')
            ->assertJsonPath('data.content_format', 'html');

        $this->assertDatabaseHas('team_updates', [
            'id' => $update->id,
            'titel' => 'Nieuwe titel',
            'excerpt' => 'Nieuwe intro',
            'content' => 'Nieuwe inhoud',
            'content_format' => 'html',
        ]);
    });

    it('allows captain to remove and add images when editing own team update', function (): void {
        Storage::fake('public');

        $update = TeamUpdate::query()->create([
            'team_id' => $this->team->id,
            'titel' => 'Met afbeeldingen',
            'content' => 'Init',
            'content_format' => 'markdown',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->withHeader('Authorization', 'Bearer '.$this->captain->createToken('pest-updates-seed')->plainTextToken)
            ->post('/api/v1/registratie/mijn/updates', [
                'titel' => 'Seed update',
                'content' => 'seed',
                'content_format' => 'markdown',
                'afbeeldingen' => [
                    UploadedFile::fake()->create('seed.jpg', 64, 'image/jpeg'),
                ],
            ])
            ->assertCreated();

        $seedUpdate = TeamUpdate::query()->where('team_id', $this->team->id)->where('titel', 'Seed update')->firstOrFail();
        $seedMediaId = (int) $seedUpdate->mediaCollectie('gallery')->firstOrFail()->id;

        // Verplaats de seed-afbeelding naar de update die we gaan bewerken, zodat remove-flow op dezelfde update test.
        $media = $seedUpdate->mediaCollectie('gallery')->firstOrFail();
        $seedUpdate->media()->detach($media->id);
        $update->koppelMedia($media, 'gallery', [
            'alt_tekst' => 'Bestaande foto',
            'onderschrift' => 'Initieel',
            'volgorde' => 0,
        ]);

        $accessToken = $this->captain->createToken('pest-updates-edit-images')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->patch('/api/v1/registratie/mijn/updates/'.$update->id, [
                'titel' => 'Met afbeeldingen bijgewerkt',
                'content' => 'Nieuwe content',
                'content_format' => 'markdown',
                'verwijder_afbeelding_ids' => [$seedMediaId],
                'afbeeldingen' => [
                    UploadedFile::fake()->create('nieuw.jpg', 64, 'image/jpeg'),
                ],
            ]);

        $response->assertOk()
            ->assertJsonPath('data.titel', 'Met afbeeldingen bijgewerkt');

        $update->refresh()->load('media');
        expect($update->mediaCollectie('gallery')->count())->toBe(1);
    });

    it('blocks unauthenticated update posting', function (): void {
        $this->postJson('/api/v1/registratie/mijn/updates', [
            'titel' => 'Niet toegestaan',
            'content' => 'Anoniem posten',
            'content_format' => 'markdown',
        ])->assertUnauthorized();
    });

    it('blocks unauthenticated update editing', function (): void {
        $update = TeamUpdate::query()->create([
            'team_id' => $this->team->id,
            'titel' => 'Niet wijzigen',
            'content' => 'Anoniem',
            'content_format' => 'markdown',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->patchJson('/api/v1/registratie/mijn/updates/'.$update->id, [
            'titel' => 'Poging',
            'content' => 'Poging',
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
