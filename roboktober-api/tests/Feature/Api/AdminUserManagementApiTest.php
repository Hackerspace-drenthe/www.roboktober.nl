<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;

describe('Admin user management API', function (): void {
    it('allows admin to list users and update another user role', function (): void {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $targetUser = User::factory()->create([
            'role' => UserRole::Visitor,
            'email' => 'captain.target@example.test',
        ]);

        $token = $admin->createToken('admin')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/users')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $targetUser->id,
                'email' => 'captain.target@example.test',
            ]);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/admin/users/'.$targetUser->id.'/role', [
                'role' => UserRole::Moderator->value,
            ])
            ->assertOk()
            ->assertJsonPath('data.role', UserRole::Moderator->value);

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'role' => UserRole::Moderator->value,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $admin->id,
            'action' => 'user.role_updated',
            'subject_type' => User::class,
            'subject_id' => $targetUser->id,
        ]);
    });

    it('blocks moderators from user role management', function (): void {
        $moderator = User::factory()->create([
            'role' => UserRole::Moderator,
        ]);

        $targetUser = User::factory()->create([
            'role' => UserRole::Visitor,
        ]);

        $token = $moderator->createToken('moderator')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/users')
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/admin/users/'.$targetUser->id.'/role', [
                'role' => UserRole::TeamCaptain->value,
            ])
            ->assertForbidden();
    });

    it('prevents admin from changing own role', function (): void {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $token = $admin->createToken('admin-self')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/admin/users/'.$admin->id.'/role', [
                'role' => UserRole::Visitor->value,
            ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Je kunt je eigen rol niet aanpassen via deze endpoint.');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => UserRole::Admin->value,
        ]);
    });
});
