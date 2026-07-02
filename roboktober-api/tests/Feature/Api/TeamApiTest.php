<?php

declare(strict_types=1);

use App\Enums\TeamStatus;
use App\Models\Team;

describe('GET /api/v1/teams', function (): void {
    it('returns only approved teams', function (): void {
        Team::factory()->create(['status' => TeamStatus::Pending]);
        Team::factory()->create(['status' => TeamStatus::Rejected]);
        $approved = Team::factory()->create(['status' => TeamStatus::Approved, 'naam' => 'Test Team']);

        $response = $this->getJson('/api/v1/teams');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.naam', 'Test Team');
    });

    it('returns expected JSON structure', function (): void {
        Team::factory()->create(['status' => TeamStatus::Approved]);

        $response = $this->getJson('/api/v1/teams');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'naam', 'status', 'status_label', 'robots']],
            ]);
    });

    it('does not expose email in response', function (): void {
        Team::factory()->create(['status' => TeamStatus::Approved, 'email' => 'secret@example.com']);

        $response = $this->getJson('/api/v1/teams');

        $response->assertOk();
        expect($response->getContent())->not->toContain('secret@example.com');
    });
});

describe('GET /api/v1/teams/{id}', function (): void {
    it('returns a single approved team', function (): void {
        $team = Team::factory()->create(['status' => TeamStatus::Approved, 'naam' => 'Solo Team']);

        $response = $this->getJson("/api/v1/teams/{$team->id}");

        $response->assertOk()
            ->assertJsonPath('data.naam', 'Solo Team');
    });

    it('returns 404 for non-approved team', function (): void {
        $team = Team::factory()->create(['status' => TeamStatus::Pending]);

        $this->getJson("/api/v1/teams/{$team->id}")->assertNotFound();
    });

    it('returns 404 for unknown team', function (): void {
        $this->getJson('/api/v1/teams/99999')->assertNotFound();
    });
});

describe('GET /api/v1/teams/{id}/robots', function (): void {
    it('returns robots for an approved team', function (): void {
        $team = Team::factory()->hasRobots(2)->create(['status' => TeamStatus::Approved]);

        $response = $this->getJson("/api/v1/teams/{$team->id}/robots");

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    });

    it('returns 404 for pending team robots', function (): void {
        $team = Team::factory()->create(['status' => TeamStatus::Pending]);

        $this->getJson("/api/v1/teams/{$team->id}/robots")->assertNotFound();
    });
});
