<?php

declare(strict_types=1);

use App\Enums\TeamStatus;
use App\Enums\UserRole;
use App\Models\Team;
use App\Models\User;

describe('Admin team moderation API', function (): void {
    it('blocks visitors from admin team moderation routes', function (): void {
        $visitor = User::factory()->create([
            'role' => UserRole::Visitor,
        ]);

        $token = $visitor->createToken('visitor')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/teams')
            ->assertForbidden();
    });

    it('allows moderators to list all teams and moderate status', function (): void {
        $moderator = User::factory()->create([
            'role' => UserRole::Moderator,
        ]);

        $pending = Team::factory()->create([
            'status' => TeamStatus::Pending,
        ]);

        $approved = Team::factory()->create([
            'status' => TeamStatus::Approved,
        ]);

        $token = $moderator->createToken('moderator')->plainTextToken;

        $listResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/teams')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $listedIds = array_map(
            static fn (array $team): int => (int) $team['id'],
            (array) $listResponse->json('data'),
        );

        expect($listedIds)->toContain($pending->id, $approved->id);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/admin/teams/'.$pending->id.'/status', [
                'status' => TeamStatus::Approved->value,
                'opmerkingen' => 'Goedgekeurd na controle.',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', TeamStatus::Approved->value);

        $this->assertDatabaseHas('teams', [
            'id' => $pending->id,
            'status' => TeamStatus::Approved->value,
            'opmerkingen' => 'Goedgekeurd na controle.',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $moderator->id,
            'action' => 'team.status_updated',
            'subject_type' => Team::class,
            'subject_id' => $pending->id,
        ]);
    });
});
