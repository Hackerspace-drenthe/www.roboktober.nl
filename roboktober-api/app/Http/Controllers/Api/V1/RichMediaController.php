<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AttachRichMediaRequest;
use App\Http\Requests\Api\V1\StoreRichMediaUploadRequest;
use App\Http\Resources\Api\V1\RichMediaResource;
use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Models\ProgrammaItem;
use App\Models\Robot;
use App\Models\Team;
use App\Models\TeamUpdate;
use App\Models\User;
use App\Services\Uploads\FilesystemMediaStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class RichMediaController extends Controller
{
    public function __construct(private readonly FilesystemMediaStorage $storage) {}

    public function index(): JsonResponse
    {
        $q = request()->query('q');

        $media = Media::query()
            ->when(is_string($q) && $q !== '', static function ($query) use ($q): void {
                $query->where('naam', 'like', '%'.$q.'%')
                    ->orWhere('bestandsnaam', 'like', '%'.$q.'%')
                    ->orWhere('mime_type', 'like', '%'.$q.'%');
            })
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return response()->json($media->through(fn (Media $item) => (new RichMediaResource($item))->resolve()));
    }

    public function upload(StoreRichMediaUploadRequest $request): JsonResponse
    {
        /** @var User $actor */
        $actor = $request->user();

        /** @var array<string, mixed> $validated */
        $validated = $request->validated();

        $file = $request->file('bestand');
        if (! $file instanceof UploadedFile) {
            return response()->json(['message' => 'Geen geldig bestand ontvangen.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $stored = $this->storage->storeUploadedFile(
            file: $file,
            directory: $this->directoryFor($file->getMimeType() ?? 'application/octet-stream', strtolower((string) $file->getClientOriginalExtension())),
            disk: 'public',
        );

        $media = Media::query()->create([
            'naam' => (string) ($validated['naam'] ?? pathinfo($stored->originalName, PATHINFO_FILENAME)),
            'bestandsnaam' => $stored->originalName,
            'pad' => $stored->path,
            'disk' => $stored->disk,
            'mime_type' => $stored->mimeType,
            'extensie' => $stored->extension,
            'grootte' => $stored->size,
            'hash' => $stored->sha256,
            'meta' => [
                'bron' => 'rich_media_upload',
            ],
            'versie' => '1.0.0',
            'downloads' => 0,
            'geupload_door' => $actor->id,
        ]);

        $attachedTo = null;

        if (isset($validated['target_type'], $validated['target_id'])) {
            $target = $this->resolveTarget((string) $validated['target_type'], (int) $validated['target_id']);
            $this->authorizeTargetMutation($actor, $target);

            $collectie = (string) ($validated['collectie'] ?? 'default');

            if ($target instanceof Robot && $collectie === 'foto') {
                $existingFotoIds = $target->mediaCollectie('foto')->pluck('media.id')->all();

                if ($existingFotoIds !== []) {
                    $target->media()->detach($existingFotoIds);
                }
            }

            if ($target instanceof User && $collectie === 'foto') {
                $existingFotoIds = $target->mediaCollectie('foto')->pluck('media.id')->all();

                if ($existingFotoIds !== []) {
                    $target->media()->detach($existingFotoIds);
                }
            }

            $target->koppelMedia($media, $collectie, [
                'alt_tekst' => $validated['alt_tekst'] ?? null,
                'onderschrift' => $validated['onderschrift'] ?? null,
                'volgorde' => $validated['volgorde'] ?? 0,
            ]);

            $attachedTo = [
                'type' => (string) $validated['target_type'],
                'id' => (int) $validated['target_id'],
                'collectie' => $collectie,
            ];
        }

        return response()->json([
            'message' => 'Bestand succesvol geupload.',
            'data' => new RichMediaResource($media),
            'attached_to' => $attachedTo,
        ], Response::HTTP_CREATED);
    }

    public function attach(AttachRichMediaRequest $request, Media $media): JsonResponse
    {
        /** @var User $actor */
        $actor = $request->user();

        /** @var array<string, mixed> $validated */
        $validated = $request->validated();

        $target = $this->resolveTarget((string) $validated['target_type'], (int) $validated['target_id']);
        $this->authorizeTargetMutation($actor, $target);

        $collectie = (string) ($validated['collectie'] ?? 'default');

        $target->koppelMedia($media, $collectie, [
            'alt_tekst' => $validated['alt_tekst'] ?? null,
            'onderschrift' => $validated['onderschrift'] ?? null,
            'volgorde' => $validated['volgorde'] ?? 0,
        ]);

        return response()->json([
            'message' => 'Bestand gekoppeld aan content.',
            'data' => new RichMediaResource($media),
            'attached_to' => [
                'type' => (string) $validated['target_type'],
                'id' => (int) $validated['target_id'],
                'collectie' => $collectie,
            ],
        ], Response::HTTP_OK);
    }

    private function directoryFor(string $mimeType, string $extension): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'content/images';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return 'content/videos';
        }

        if (in_array($extension, ['stl', 'obj', '3mf'], true) || str_starts_with($mimeType, 'model/')) {
            return 'content/models';
        }

        return 'content/files';
    }

    private function resolveTarget(string $type, int $id): Model
    {
        return match ($type) {
            'post' => Post::query()->findOrFail($id),
            'page' => Page::query()->findOrFail($id),
            'team' => Team::query()->findOrFail($id),
            'robot' => Robot::query()->with('team')->findOrFail($id),
            'team_update' => TeamUpdate::query()->with('team')->findOrFail($id),
            'user' => User::query()->findOrFail($id),
            'programma_item' => ProgrammaItem::query()->findOrFail($id),
            default => abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Onbekend target_type.'),
        };
    }

    private function authorizeTargetMutation(User $actor, Model $target): void
    {
        if ($actor->hasAnyRole(UserRole::Admin, UserRole::Moderator)) {
            return;
        }

        if ($target instanceof Team && $target->captain_user_id === $actor->id) {
            return;
        }

        if ($target instanceof TeamUpdate && $target->team?->captain_user_id === $actor->id) {
            return;
        }

        if ($target instanceof Robot && $target->team?->captain_user_id === $actor->id) {
            return;
        }

        if ($target instanceof User && $target->id === $actor->id) {
            return;
        }

        abort(Response::HTTP_FORBIDDEN, 'Geen rechten om media aan dit item te koppelen.');
    }
}
