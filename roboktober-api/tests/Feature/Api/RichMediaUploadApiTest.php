<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Post;
use App\Models\Team;
use App\Models\TeamUpdate;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

describe('Rich media upload API', function (): void {
    it('allows moderator to upload and attach media to a post', function (): void {
        Storage::fake('public');

        $moderator = User::factory()->create([
            'role' => UserRole::Moderator,
        ]);

        $post = Post::factory()->create();

        $token = $moderator->createToken('pest-media')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->post('/api/v1/media/upload', [
                'bestand' => UploadedFile::fake()->create('hero.jpg', 256, 'image/jpeg'),
                'target_type' => 'post',
                'target_id' => $post->id,
                'collectie' => 'featured',
                'alt_tekst' => 'Headerafbeelding',
            ]);

        $response->assertCreated()
            ->assertJsonPath('attached_to.type', 'post')
            ->assertJsonPath('attached_to.id', $post->id)
            ->assertJsonPath('attached_to.collectie', 'featured')
            ->assertJsonStructure([
                'data' => ['id', 'url', 'html_snippet', 'markdown_snippet'],
            ]);

        $post->refresh()->load('media');

        expect($post->mediaCollectie('featured')->count())->toBe(1);
    });

    it('allows teamcaptain to attach media to own team update', function (): void {
        Storage::fake('public');

        $captain = User::factory()->create([
            'role' => UserRole::TeamCaptain,
        ]);

        $team = Team::factory()->create([
            'captain_user_id' => $captain->id,
        ]);

        $update = TeamUpdate::query()->create([
            'team_id' => $team->id,
            'titel' => 'Update met media',
            'excerpt' => 'Media test',
            'content' => 'Test content',
            'content_format' => 'markdown',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $token = $captain->createToken('pest-media')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->post('/api/v1/media/upload', [
                'bestand' => UploadedFile::fake()->create('teamfoto.jpg', 300, 'image/jpeg'),
                'target_type' => 'team_update',
                'target_id' => $update->id,
                'collectie' => 'bijlagen',
            ]);

        $response->assertCreated()
            ->assertJsonPath('attached_to.type', 'team_update')
            ->assertJsonPath('attached_to.id', $update->id);

        $update->refresh()->load('media');

        expect($update->mediaCollectie('bijlagen')->count())->toBe(1);
    });

    it('blocks visitor from rich media upload', function (): void {
        Storage::fake('public');

        $visitor = User::factory()->create([
            'role' => UserRole::Visitor,
        ]);

        $token = $visitor->createToken('pest-media')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/media/upload', [
                'target_type' => 'post',
                'target_id' => 1,
            ])
            ->assertForbidden();
    });
});
