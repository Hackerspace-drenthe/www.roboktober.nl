<?php

declare(strict_types=1);

use App\Enums\ContentFormat;
use App\Enums\TeamStatus;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\Page;
use App\Models\Post;
use App\Models\Team;
use App\Models\TeamUpdate;
use App\Models\User;

describe('Admin dashboard summary API', function (): void {
    it('allows moderator to fetch summary metrics', function (): void {
        $moderator = User::factory()->create([
            'role' => UserRole::Moderator,
        ]);

        Team::factory()->create(['status' => TeamStatus::Pending]);
        Post::factory()->create(['is_published' => false]);

        Page::query()->create([
            'slug' => 'veiligheid',
            'titel' => 'Veiligheid',
            'content' => '<p>Regels</p>',
            'content_format' => ContentFormat::Html,
            'seo' => null,
            'is_published' => false,
            'published_at' => null,
        ]);

        $team = Team::factory()->create(['status' => TeamStatus::Approved]);

        TeamUpdate::query()->create([
            'team_id' => $team->id,
            'titel' => 'Werkplaats update',
            'excerpt' => null,
            'content' => '<p>Voortgang</p>',
            'content_format' => ContentFormat::Html,
            'is_published' => false,
            'published_at' => null,
        ]);

        AuditLog::query()->create([
            'actor_user_id' => $moderator->id,
            'action' => 'team.status_updated',
            'subject_type' => Team::class,
            'subject_id' => $team->id,
            'before' => ['status' => 'pending'],
            'after' => ['status' => 'approved'],
            'context' => null,
        ]);

        $token = $moderator->createToken('moderator')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/dashboard-summary')
            ->assertOk()
            ->assertJsonPath('data.stats.pending_teams', 1)
            ->assertJsonPath('data.stats.draft_posts', 1)
            ->assertJsonPath('data.stats.draft_pages', 1)
            ->assertJsonPath('data.stats.draft_team_updates', 1)
            ->assertJsonCount(1, 'data.recent_activity');
    });

    it('blocks visitors from dashboard summary', function (): void {
        $visitor = User::factory()->create([
            'role' => UserRole::Visitor,
        ]);

        $token = $visitor->createToken('visitor')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/dashboard-summary')
            ->assertForbidden();
    });
});
