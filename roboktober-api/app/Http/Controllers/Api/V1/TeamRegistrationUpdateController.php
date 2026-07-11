<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Uploads\MediaStorage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTeamUpdateRequest;
use App\Http\Requests\Api\V1\UpdateTeamUpdateRequest;
use App\Http\Resources\Api\V1\TeamUpdateResource;
use App\Models\Media;
use App\Models\Team;
use App\Models\TeamUpdate;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class TeamRegistrationUpdateController extends Controller
{
    public function __construct(private readonly MediaStorage $storage) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $team = $this->resolveTeamForUser($this->resolveAuthenticatedUser($request));

        $updates = TeamUpdate::query()
            ->where('team_id', $team->id)
            ->latest('published_at')
            ->latest('id')
            ->with('media')
            ->get();

        return TeamUpdateResource::collection($updates);
    }

    public function store(StoreTeamUpdateRequest $request): JsonResponse
    {
        $team = $this->resolveTeamForUser($this->resolveAuthenticatedUser($request));

        /** @var array{titel: string, excerpt?: string|null, content: string, content_format: string} $validated */
        $validated = $request->validated();

        /** @var TeamUpdate $update */
        $update = DB::transaction(function () use ($validated, $request, $team): TeamUpdate {
            $update = TeamUpdate::query()->create([
                'team_id' => $team->id,
                'titel' => (string) $validated['titel'],
                'excerpt' => isset($validated['excerpt']) ? (string) $validated['excerpt'] : null,
                'content' => (string) $validated['content'],
                'content_format' => (string) $validated['content_format'],
                'is_published' => true,
                'published_at' => now(),
            ]);

            $bestanden = $this->normalizeUploadedFiles($request->file('afbeeldingen'));

            foreach ($bestanden as $index => $bestand) {
                $stored = $this->storage->storeUploadedFile(
                    file: $bestand,
                    directory: 'team-updates',
                    disk: 'public',
                );

                $media = Media::query()->create([
                    'naam' => 'Team update '.$team->naam.' - '.$update->titel,
                    'bestandsnaam' => $stored->originalName,
                    'pad' => $stored->path,
                    'disk' => $stored->disk,
                    'mime_type' => $stored->mimeType,
                    'extensie' => $stored->extension,
                    'grootte' => $stored->size,
                    'hash' => $stored->sha256,
                    'meta' => [
                        'bron' => 'team_update',
                        'orig_name' => $stored->originalName,
                    ],
                    'versie' => '1.0.0',
                    'downloads' => 0,
                ]);

                $update->koppelMedia($media, 'gallery', [
                    'alt_tekst' => 'Voortgangsfoto van '.$team->naam,
                    'onderschrift' => 'Ingestuurd via accountbeheer',
                    'volgorde' => $index,
                    'meta' => ['uuid' => (string) Str::uuid()],
                ]);
            }

            return $update;
        });

        $update->load('media');

        return (new TeamUpdateResource($update))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateTeamUpdateRequest $request, TeamUpdate $teamUpdate): JsonResponse
    {
        $team = $this->resolveTeamForUser($this->resolveAuthenticatedUser($request));

        if ($teamUpdate->team_id !== $team->id) {
            abort(Response::HTTP_NOT_FOUND);
        }

        /** @var array{titel: string, excerpt?: string|null, content: string, content_format: string, verwijder_afbeelding_ids?: list<int>} $validated */
        $validated = $request->validated();

        /** @var list<int> $verwijderAfbeeldingIds */
        $verwijderAfbeeldingIds = $validated['verwijder_afbeelding_ids'] ?? [];

        DB::transaction(function () use ($request, $teamUpdate, $validated, $verwijderAfbeeldingIds): void {
            $teamUpdate->forceFill([
                'titel' => (string) $validated['titel'],
                'excerpt' => isset($validated['excerpt']) ? (string) $validated['excerpt'] : null,
                'content' => (string) $validated['content'],
                'content_format' => (string) $validated['content_format'],
            ])->save();

            if ($verwijderAfbeeldingIds !== []) {
                $galleryMedia = $teamUpdate->mediaCollectie('gallery')->get()->keyBy('id');

                foreach ($verwijderAfbeeldingIds as $mediaId) {
                    if (! $galleryMedia->has($mediaId)) {
                        throw ValidationException::withMessages([
                            'verwijder_afbeelding_ids' => ['Een geselecteerde afbeelding hoort niet bij dit voortgangsbericht.'],
                        ]);
                    }
                }

                $teamUpdate->media()->detach($verwijderAfbeeldingIds);
            }

            $bestanden = $this->normalizeUploadedFiles($request->file('afbeeldingen'));
            $maxVolgorde = $teamUpdate->mediaCollectie('gallery')->max('mediables.volgorde');
            $volgordeStart = (is_numeric($maxVolgorde) ? (int) $maxVolgorde : -1) + 1;

            foreach ($bestanden as $index => $bestand) {
                $stored = $this->storage->storeUploadedFile(
                    file: $bestand,
                    directory: 'team-updates',
                    disk: 'public',
                );

                $media = Media::query()->create([
                    'naam' => 'Team update '.$teamUpdate->team_id.' - '.$teamUpdate->titel,
                    'bestandsnaam' => $stored->originalName,
                    'pad' => $stored->path,
                    'disk' => $stored->disk,
                    'mime_type' => $stored->mimeType,
                    'extensie' => $stored->extension,
                    'grootte' => $stored->size,
                    'hash' => $stored->sha256,
                    'meta' => [
                        'bron' => 'team_update_edit',
                        'orig_name' => $stored->originalName,
                    ],
                    'versie' => '1.0.0',
                    'downloads' => 0,
                ]);

                $teamUpdate->koppelMedia($media, 'gallery', [
                    'alt_tekst' => 'Voortgangsfoto van team update '.$teamUpdate->id,
                    'onderschrift' => 'Bijgewerkt via accountbeheer',
                    'volgorde' => $volgordeStart + $index,
                    'meta' => ['uuid' => (string) Str::uuid()],
                ]);
            }
        });

        $teamUpdate->load('media');

        return (new TeamUpdateResource($teamUpdate))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    private function resolveAuthenticatedUser(Request $request): User
    {
        /** @var User|null $user */
        $user = $request->user();

        if ($user === null) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        return $user;
    }

    private function resolveTeamForUser(User $user): Team
    {
        return Team::query()->where('captain_user_id', $user->id)->firstOrFail();
    }

    /**
     * @return list<UploadedFile>
     */
    private function normalizeUploadedFiles(mixed $files): array
    {
        if ($files instanceof UploadedFile) {
            return [$files];
        }

        if (! is_array($files)) {
            return [];
        }

        /** @var list<UploadedFile> $normalized */
        $normalized = array_values(array_filter(
            $files,
            static fn (mixed $file): bool => $file instanceof UploadedFile,
        ));

        return $normalized;
    }
}
