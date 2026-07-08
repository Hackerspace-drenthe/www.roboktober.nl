<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Contracts\Uploads\MediaStorage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTeamUpdateRequest;
use App\Http\Resources\Api\V1\TeamUpdateResource;
use App\Models\Media;
use App\Models\Team;
use App\Models\TeamUpdate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TeamRegistrationUpdateController extends Controller
{
    public function __construct(private readonly MediaStorage $storage)
    {
    }

    public function index(string $token): AnonymousResourceCollection
    {
        $team = $this->resolveTeamByToken($token);

        $updates = TeamUpdate::query()
            ->where('team_id', $team->id)
            ->latest('published_at')
            ->latest('id')
            ->with('media')
            ->get();

        return TeamUpdateResource::collection($updates);
    }

    public function store(StoreTeamUpdateRequest $request, string $token): JsonResponse
    {
        $team = $this->resolveTeamByToken($token);
        $this->authorizeTeamMutation($request, $team);

        /** @var array<string, mixed> $validated */
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

            $bestanden = $request->file('afbeeldingen', []);

            foreach ($bestanden as $index => $bestand) {
                if (!($bestand instanceof \Illuminate\Http\UploadedFile)) {
                    continue;
                }

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
                    'onderschrift' => 'Ingestuurd via bewerklink',
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

    private function authorizeTeamMutation(Request $request, Team $team): void
    {
        $user = $request->user();

        if ($user === null) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        if ($user->hasAnyRole(UserRole::Admin, UserRole::Moderator)) {
            return;
        }

        if ($team->captain_user_id !== $user->id) {
            abort(Response::HTTP_FORBIDDEN, 'Alleen de gekoppelde teamcaptain mag voortgang plaatsen.');
        }
    }

    private function resolveTeamByToken(string $token): Team
    {
        $tokenHash = hash('sha256', $token);

        return Team::query()
            ->where('edit_token_hash', $tokenHash)
            ->whereNotNull('edit_token_expires_at')
            ->where('edit_token_expires_at', '>', now())
            ->firstOrFail();
    }
}
