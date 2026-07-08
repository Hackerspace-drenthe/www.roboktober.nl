<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Post;
use App\Models\Robot;
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

    it('allows moderator to upload an stl model', function (): void {
        Storage::fake('public');

        $moderator = User::factory()->create([
            'role' => UserRole::Moderator,
        ]);

        $token = $moderator->createToken('pest-media-stl')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->post('/api/v1/media/upload', [
                'bestand' => UploadedFile::fake()->create('robot-chassis.stl', 256, 'application/sla'),
                'naam' => 'Robot Chassis STL',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.extensie', 'stl');

        $uploadedUrl = (string) $response->json('data.url');
        expect($uploadedUrl)->toEndWith('.stl');
    });

    it('keeps original extension for uploaded resource files', function (): void {
        Storage::fake('public');

        $moderator = User::factory()->create([
            'role' => UserRole::Moderator,
        ]);

        $token = $moderator->createToken('pest-media-ext')->plainTextToken;

        $pdfResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->post('/api/v1/media/upload', [
                'bestand' => UploadedFile::fake()->create('build-guide.pdf', 128, 'application/pdf'),
                'naam' => 'Build Guide',
            ]);

        $pdfResponse->assertCreated()
            ->assertJsonPath('data.extensie', 'pdf');
        expect((string) $pdfResponse->json('data.url'))->toEndWith('.pdf');

        $zipResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->post('/api/v1/media/upload', [
                'bestand' => UploadedFile::fake()->create('assets-pack.zip', 256, 'application/octet-stream'),
                'naam' => 'Assets Pack',
            ]);

        $zipResponse->assertCreated()
            ->assertJsonPath('data.extensie', 'zip');
        expect((string) $zipResponse->json('data.url'))->toEndWith('.zip');
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

    it('allows teamcaptain to upload robot photo to own robot', function (): void {
        Storage::fake('public');

        $captain = User::factory()->create([
            'role' => UserRole::TeamCaptain,
        ]);

        $team = Team::factory()->create([
            'captain_user_id' => $captain->id,
        ]);

        $robot = Robot::factory()->create([
            'team_id' => $team->id,
        ]);

        $token = $captain->createToken('pest-media-robot-own')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->post('/api/v1/media/upload', [
                'bestand' => UploadedFile::fake()->create('robot-front.jpg', 256, 'image/jpeg'),
                'target_type' => 'robot',
                'target_id' => $robot->id,
                'collectie' => 'foto',
            ])
            ->assertCreated()
            ->assertJsonPath('attached_to.type', 'robot')
            ->assertJsonPath('attached_to.id', $robot->id);

        $robot->refresh()->load('media');
        expect($robot->mediaCollectie('foto')->count())->toBe(1);
    });

    it('blocks teamcaptain from uploading robot photo to foreign robot', function (): void {
        Storage::fake('public');

        $captain = User::factory()->create([
            'role' => UserRole::TeamCaptain,
        ]);

        $otherTeam = Team::factory()->create();
        $robot = Robot::factory()->create([
            'team_id' => $otherTeam->id,
        ]);

        $token = $captain->createToken('pest-media-robot-foreign')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->post('/api/v1/media/upload', [
                'bestand' => UploadedFile::fake()->create('robot-foreign.jpg', 256, 'image/jpeg'),
                'target_type' => 'robot',
                'target_id' => $robot->id,
                'collectie' => 'foto',
            ])
            ->assertForbidden();
    });
});
