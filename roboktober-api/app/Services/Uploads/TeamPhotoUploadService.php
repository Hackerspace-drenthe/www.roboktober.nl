<?php

declare(strict_types=1);

namespace App\Services\Uploads;

use App\Contracts\Uploads\MediaStorage;
use App\Models\Media;
use App\Models\Team;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

final readonly class TeamPhotoUploadService
{
    public function __construct(private MediaStorage $storage) {}

    public function attach(Team $team, UploadedFile $photo, string $source, string $caption): Media
    {
        $stored = $this->storage->storeUploadedFile(
            file: $photo,
            directory: $this->configString('uploads.team_photo.directory', 'team-fotos'),
            disk: $this->configString('uploads.team_photo.disk', 'public'),
        );

        $media = Media::query()->create([
            'naam' => 'Teamfoto '.$team->naam,
            'bestandsnaam' => $stored->originalName,
            'pad' => $stored->path,
            'disk' => $stored->disk,
            'mime_type' => $stored->mimeType,
            'extensie' => $stored->extension,
            'grootte' => $stored->size,
            'hash' => $stored->sha256,
            'meta' => [
                'bron' => $source,
                'orig_name' => $stored->originalName,
            ],
            'versie' => '1.0.0',
            'downloads' => 0,
        ]);

        $team->koppelMedia($media, $this->configString('uploads.team_photo.collection', 'foto'), [
            'alt_tekst' => 'Teamfoto van '.$team->naam,
            'onderschrift' => $caption,
            'volgorde' => 0,
            'meta' => ['uuid' => (string) Str::uuid()],
        ]);

        return $media;
    }

    public function replace(Team $team, UploadedFile $photo, string $source, string $caption): Media
    {
        $this->remove($team);

        return $this->attach($team, $photo, $source, $caption);
    }

    public function remove(Team $team): void
    {
        $collection = $this->configString('uploads.team_photo.collection', 'foto');
        $photos = $team->mediaCollectie($collection)->get();

        foreach ($photos as $photo) {
            $team->ontkoppelMedia($photo);

            if ($photo->pad !== '') {
                $this->storage->delete($photo->pad, $photo->disk);
            }

            $photo->delete();
        }
    }

    private function configString(string $key, string $fallback): string
    {
        $value = config($key, $fallback);

        return is_string($value) && $value !== '' ? $value : $fallback;
    }
}
