<?php

declare(strict_types=1);

use App\Enums\TeamMembershipStatus;
use App\Enums\TeamStatus;
use App\Enums\UserRole;
use App\Models\Team;
use App\Models\TeamMembership;
use App\Models\User;

describe('Team membership API', function (): void {
    it('allows authenticated user to apply for an approved team', function (): void {
        $captain = User::factory()->create([
            'role' => UserRole::TeamCaptain,
        ]);

        $team = Team::factory()->create([
            'status' => TeamStatus::Approved,
            'captain_user_id' => $captain->id,
        ]);

        $visitor = User::factory()->create([
            'role' => UserRole::Visitor,
        ]);

        $token = $visitor->createToken('pest-membership')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/teams/'.$team->id.'/membership-requests', [
                'request_message' => 'Ik wil graag meebouwen.',
            ])
            ->assertCreated()
            ->assertJsonPath('data.status', TeamMembershipStatus::Pending->value);

        $this->assertDatabaseHas('team_memberships', [
            'team_id' => $team->id,
            'user_id' => $visitor->id,
            'status' => TeamMembershipStatus::Pending->value,
        ]);
    });

    it('allows teamcaptain to review pending membership requests', function (): void {
        $captain = User::factory()->create([
            'role' => UserRole::TeamCaptain,
        ]);

        $team = Team::factory()->create([
            'status' => TeamStatus::Approved,
            'captain_user_id' => $captain->id,
        ]);

        $candidate = User::factory()->create([
            'role' => UserRole::Visitor,
        ]);

        $membership = TeamMembership::query()->create([
            'team_id' => $team->id,
            'user_id' => $candidate->id,
            'status' => TeamMembershipStatus::Pending,
        ]);

        $token = $captain->createToken('pest-membership-captain')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/teams/mijn/membership-requests/'.$membership->id, [
                'status' => TeamMembershipStatus::Approved->value,
            ])
            ->assertOk()
            ->assertJsonPath('data.status', TeamMembershipStatus::Approved->value);

        $this->assertDatabaseHas('team_memberships', [
            'id' => $membership->id,
            'status' => TeamMembershipStatus::Approved->value,
        ]);
    });

    it('blocks reviewing requests from users without captain or moderation role', function (): void {
        $captain = User::factory()->create([
            'role' => UserRole::TeamCaptain,
        ]);

        $team = Team::factory()->create([
            'status' => TeamStatus::Approved,
            'captain_user_id' => $captain->id,
        ]);

        $candidate = User::factory()->create([
            'role' => UserRole::Visitor,
        ]);

        $membership = TeamMembership::query()->create([
            'team_id' => $team->id,
            'user_id' => $candidate->id,
            'status' => TeamMembershipStatus::Pending,
        ]);

        $visitor = User::factory()->create([
            'role' => UserRole::Visitor,
        ]);

        $token = $visitor->createToken('pest-membership-visitor')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/teams/mijn/membership-requests/'.$membership->id, [
                'status' => TeamMembershipStatus::Rejected->value,
            ])
            ->assertForbidden();
    });

    it('allows authenticated user to apply for a pending team', function (): void {
        $captain = User::factory()->create([
            'role' => UserRole::TeamCaptain,
        ]);

        $team = Team::factory()->create([
            'status' => TeamStatus::Pending,
            'captain_user_id' => $captain->id,
        ]);

        $visitor = User::factory()->create([
            'role' => UserRole::Visitor,
        ]);

        $token = $visitor->createToken('pest-membership-pending')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/teams/'.$team->id.'/membership-requests', [
                'request_message' => 'Ik wil helpen bouwen tijdens voorbereiding.',
            ])
            ->assertCreated()
            ->assertJsonPath('data.status', TeamMembershipStatus::Pending->value);
    });

    it('allows authenticated user to apply for a team without captain account', function (): void {
        $team = Team::factory()->create([
            'status' => TeamStatus::Pending,
            'captain_user_id' => null,
        ]);

        $visitor = User::factory()->create([
            'role' => UserRole::Visitor,
        ]);

        $token = $visitor->createToken('pest-membership-no-captain')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/teams/'.$team->id.'/membership-requests', [
                'request_message' => 'Aanmelden als lid.',
            ])
            ->assertCreated()
            ->assertJsonPath('data.status', TeamMembershipStatus::Pending->value);
    });

    it('blocks applying for a rejected team', function (): void {
        $captain = User::factory()->create([
            'role' => UserRole::TeamCaptain,
        ]);

        $team = Team::factory()->create([
            'status' => TeamStatus::Rejected,
            'captain_user_id' => $captain->id,
        ]);

        $visitor = User::factory()->create([
            'role' => UserRole::Visitor,
        ]);

        $token = $visitor->createToken('pest-membership-rejected')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/teams/'.$team->id.'/membership-requests', [
                'request_message' => 'Kan ik nog aansluiten?',
            ])
            ->assertUnprocessable();
    });
});
