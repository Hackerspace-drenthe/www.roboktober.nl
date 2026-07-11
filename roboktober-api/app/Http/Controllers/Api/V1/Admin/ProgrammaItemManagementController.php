<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreProgrammaItemRequest;
use App\Http\Requests\Api\V1\UpdateProgrammaItemRequest;
use App\Http\Resources\Api\V1\ProgrammaItemResource;
use App\Models\Edition;
use App\Models\ProgrammaItem;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class ProgrammaItemManagementController extends Controller
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function index(Edition $edition): AnonymousResourceCollection
    {
        $this->authorize('viewAny', ProgrammaItem::class);

        $items = $edition->programmaItems()
            ->with('media')
            ->orderBy('start_at')
            ->orderBy('volgorde')
            ->orderBy('id')
            ->get();

        return ProgrammaItemResource::collection($items);
    }

    public function store(StoreProgrammaItemRequest $request, Edition $edition): ProgrammaItemResource
    {
        $this->authorize('create', ProgrammaItem::class);

        /** @var User $actor */
        $actor = $request->user();

        /** @var array{titel: string, beschrijving: string, content_format: string, start_at: string, end_at?: string|null, volgorde?: int, is_published?: bool} $validated */
        $validated = $request->validated();

        $item = ProgrammaItem::query()->create([
            'edition_id' => $edition->id,
            'titel' => $validated['titel'],
            'beschrijving' => $validated['beschrijving'],
            'content_format' => $validated['content_format'],
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'] ?? null,
            'volgorde' => (int) ($validated['volgorde'] ?? 0),
            'is_published' => (bool) ($validated['is_published'] ?? true),
        ]);

        $this->audit->log(
            actor: $actor,
            action: 'programma_item.created',
            subject: $item,
            before: null,
            after: [
                'edition_id' => $item->edition_id,
                'titel' => $item->titel,
                'start_at' => $item->start_at?->toIso8601String(),
                'end_at' => $item->end_at?->toIso8601String(),
                'is_published' => $item->is_published,
            ],
        );

        $item->load('media');

        return new ProgrammaItemResource($item);
    }

    public function update(UpdateProgrammaItemRequest $request, ProgrammaItem $programmaItem): ProgrammaItemResource
    {
        $this->authorize('update', $programmaItem);

        /** @var User $actor */
        $actor = $request->user();

        /** @var array{titel?: string, beschrijving?: string, content_format?: string, start_at?: string, end_at?: string|null, volgorde?: int, is_published?: bool} $validated */
        $validated = $request->validated();

        $before = [
            'titel' => $programmaItem->titel,
            'beschrijving' => $programmaItem->beschrijving,
            'content_format' => $programmaItem->content_format->value,
            'start_at' => $programmaItem->start_at?->toIso8601String(),
            'end_at' => $programmaItem->end_at?->toIso8601String(),
            'volgorde' => $programmaItem->volgorde,
            'is_published' => $programmaItem->is_published,
        ];

        if (array_key_exists('end_at', $validated) && is_string($validated['end_at'])) {
            $startAt = $validated['start_at'] ?? $programmaItem->start_at?->toIso8601String();
            if (is_string($startAt) && strtotime($validated['end_at']) < strtotime($startAt)) {
                throw ValidationException::withMessages([
                    'end_at' => ['Eindtijd moet op of na starttijd liggen.'],
                ]);
            }
        }

        $programmaItem->fill($validated);
        $programmaItem->save();

        $this->audit->log(
            actor: $actor,
            action: 'programma_item.updated',
            subject: $programmaItem,
            before: $before,
            after: [
                'titel' => $programmaItem->titel,
                'beschrijving' => $programmaItem->beschrijving,
                'content_format' => $programmaItem->content_format->value,
                'start_at' => $programmaItem->start_at?->toIso8601String(),
                'end_at' => $programmaItem->end_at?->toIso8601String(),
                'volgorde' => $programmaItem->volgorde,
                'is_published' => $programmaItem->is_published,
            ],
        );

        $programmaItem->load('media');

        return new ProgrammaItemResource($programmaItem);
    }

    public function destroy(Request $request, ProgrammaItem $programmaItem): JsonResponse
    {
        $this->authorize('delete', $programmaItem);

        /** @var User $actor */
        $actor = $request->user();

        $before = [
            'edition_id' => $programmaItem->edition_id,
            'titel' => $programmaItem->titel,
            'start_at' => $programmaItem->start_at?->toIso8601String(),
            'end_at' => $programmaItem->end_at?->toIso8601String(),
            'is_published' => $programmaItem->is_published,
        ];

        $programmaItem->delete();

        $this->audit->log(
            actor: $actor,
            action: 'programma_item.deleted',
            subject: $programmaItem,
            before: $before,
            after: null,
        );

        return response()->json([
            'message' => 'Programma-item verwijderd.',
        ]);
    }
}
