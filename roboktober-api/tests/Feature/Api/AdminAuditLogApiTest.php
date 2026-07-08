<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\User;

describe('Admin audit log API', function (): void {
    it('allows admin to list audit logs', function (): void {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $actor = User::factory()->create([
            'role' => UserRole::Moderator,
        ]);

        AuditLog::query()->create([
            'actor_user_id' => $actor->id,
            'action' => 'team.status_updated',
            'subject_type' => App\Models\Team::class,
            'subject_id' => 42,
            'before' => ['status' => 'pending'],
            'after' => ['status' => 'approved'],
            'context' => null,
        ]);

        $token = $admin->createToken('admin')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/audit-logs')
            ->assertOk()
            ->assertJsonFragment([
                'action' => 'team.status_updated',
                'subject_id' => 42,
            ]);
    });

    it('blocks moderator from audit logs', function (): void {
        $moderator = User::factory()->create([
            'role' => UserRole::Moderator,
        ]);

        $token = $moderator->createToken('moderator')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/audit-logs')
            ->assertForbidden();
    });
});
