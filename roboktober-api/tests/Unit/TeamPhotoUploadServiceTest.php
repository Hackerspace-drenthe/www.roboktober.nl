<?php

declare(strict_types=1);

use App\Models\Team;
use App\Services\Uploads\TeamPhotoUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

describe('TeamPhotoUploadService', function (): void {
    beforeEach(function (): void {
        Storage::fake('public');
        config()->set('uploads.team_photo.disk', 'public');
        config()->set('uploads.team_photo.directory', 'team-fotos');
        config()->set('uploads.team_photo.collection', 'foto');

        $this->team = Team::factory()->create();
        $this->service = app(TeamPhotoUploadService::class);
    });

    it('stores a team photo and links media to team', function (): void {
        $file = UploadedFile::fake()->create('team.jpg', 256, 'image/jpeg');

        $media = $this->service->attach(
            team: $this->team,
            photo: $file,
            source: 'test_upload',
            caption: 'Test upload caption',
        );

        $this->team->refresh();

        expect($this->team->mediaCollectie('foto')->count())->toBe(1);
        expect($media->pad)->toStartWith('team-fotos/');
        Storage::disk('public')->assertExists($media->pad);
    });

    it('replaces existing photo and removes old file', function (): void {
        $first = $this->service->attach(
            team: $this->team,
            photo: UploadedFile::fake()->create('old.jpg', 256, 'image/jpeg'),
            source: 'test_old',
            caption: 'Old caption',
        );

        $oldPath = $first->pad;

        $second = $this->service->replace(
            team: $this->team,
            photo: UploadedFile::fake()->create('new.jpg', 256, 'image/jpeg'),
            source: 'test_new',
            caption: 'New caption',
        );

        $this->team->refresh();

        expect($this->team->mediaCollectie('foto')->count())->toBe(1);
        expect($second->id)->not->toBe($first->id);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($second->pad);
    });

    it('removes photo media and physical files', function (): void {
        $media = $this->service->attach(
            team: $this->team,
            photo: UploadedFile::fake()->create('remove.jpg', 256, 'image/jpeg'),
            source: 'test_remove',
            caption: 'Remove caption',
        );

        $this->service->remove($this->team);

        $this->team->refresh();

        expect($this->team->mediaCollectie('foto')->count())->toBe(0);
        Storage::disk('public')->assertMissing($media->pad);
        $this->assertSoftDeleted('media', ['id' => $media->id]);
    });
});
