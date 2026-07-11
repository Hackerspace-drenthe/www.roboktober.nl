<?php

declare(strict_types=1);

use App\Enums\ContentFormat;
use App\Enums\TeamStatus;
use App\Enums\UserRole;
use App\Models\Page;
use App\Models\Post;
use App\Models\Team;
use App\Models\TeamUpdate;
use App\Models\User;

describe('Admin content moderation API', function (): void {
    it('blocks visitors from content moderation routes', function (): void {
        $visitor = User::factory()->create([
            'role' => UserRole::Visitor,
        ]);

        $token = $visitor->createToken('visitor')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/posts')
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/pages')
            ->assertForbidden();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/team-updates')
            ->assertForbidden();
    });

    it('allows moderator to publish and unpublish posts, pages and team updates', function (): void {
        $moderator = User::factory()->create([
            'role' => UserRole::Moderator,
        ]);

        $post = Post::factory()->create([
            'is_published' => false,
            'published_at' => null,
        ]);

        $page = Page::query()->create([
            'slug' => 'regels-en-veiligheid',
            'titel' => 'Regels en veiligheid',
            'content' => '<p>Veiligheid eerst.</p>',
            'content_format' => ContentFormat::Html,
            'seo' => null,
            'is_published' => false,
            'published_at' => null,
        ]);

        $team = Team::factory()->create([
            'status' => TeamStatus::Approved,
        ]);

        $teamUpdate = TeamUpdate::query()->create([
            'team_id' => $team->id,
            'titel' => 'Armor update',
            'excerpt' => 'Nieuwe zijpanelen',
            'content' => '<p>Update tekst</p>',
            'content_format' => ContentFormat::Html,
            'is_published' => false,
            'published_at' => null,
        ]);

        $token = $moderator->createToken('moderator')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/posts')
            ->assertOk()
            ->assertJsonFragment(['id' => $post->id]);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/admin/posts/'.$post->id.'/status', [
                'is_published' => true,
            ])
            ->assertOk()
            ->assertJsonPath('data.is_published', true);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/pages')
            ->assertOk()
            ->assertJsonFragment(['id' => $page->id]);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/admin/pages/'.$page->id.'/status', [
                'is_published' => true,
            ])
            ->assertOk()
            ->assertJsonPath('data.is_published', true);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/team-updates')
            ->assertOk()
            ->assertJsonFragment(['id' => $teamUpdate->id]);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/v1/admin/team-updates/'.$teamUpdate->id.'/status', [
                'is_published' => true,
            ])
            ->assertOk()
            ->assertJsonPath('data.is_published', true);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'is_published' => true,
        ]);

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'is_published' => true,
        ]);

        $this->assertDatabaseHas('team_updates', [
            'id' => $teamUpdate->id,
            'is_published' => true,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $moderator->id,
            'action' => 'post.publish_state_updated',
            'subject_type' => Post::class,
            'subject_id' => $post->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $moderator->id,
            'action' => 'page.publish_state_updated',
            'subject_type' => Page::class,
            'subject_id' => $page->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $moderator->id,
            'action' => 'team_update.publish_state_updated',
            'subject_type' => TeamUpdate::class,
            'subject_id' => $teamUpdate->id,
        ]);
    });
});
