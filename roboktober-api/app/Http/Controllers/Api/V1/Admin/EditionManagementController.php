<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreEditionRequest;
use App\Http\Requests\Api\V1\UpdateEditionRequest;
use App\Http\Resources\Api\V1\EditionResource;
use App\Models\Edition;
use App\Models\Location;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class EditionManagementController extends Controller
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Edition::class);

        $status = $request->query('status');
        $zoekterm = $request->query('q');

        $edities = Edition::query()
            ->with('location')
            ->when($status === 'open', static fn ($query) => $query->where('is_done', false))
            ->when($status === 'done', static fn ($query) => $query->where('is_done', true))
            ->when(is_string($zoekterm) && $zoekterm !== '', static function ($query) use ($zoekterm): void {
                $query
                    ->where('naam', 'like', '%'.$zoekterm.'%')
                    ->orWhereHas('location', static function ($locationQuery) use ($zoekterm): void {
                        $locationQuery
                            ->where('name', 'like', '%'.$zoekterm.'%')
                            ->orWhere('address', 'like', '%'.$zoekterm.'%')
                            ->orWhere('place', 'like', '%'.$zoekterm.'%')
                            ->orWhere('zipcode', 'like', '%'.$zoekterm.'%');
                    });
            })
            ->orderByDesc('start_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return EditionResource::collection($edities);
    }

    public function store(StoreEditionRequest $request): EditionResource
    {
        $this->authorize('create', Edition::class);

        /** @var User $actor */
        $actor = $request->user();

        /** @var array{naam: string, location: array{name: string, address: string, place: string, zipcode: string, osm_url?: string|null, instructions?: string|null}, omschrijving?: string|null, start_at: string, end_at?: string|null, is_done?: bool} $validated */
        $validated = $request->validated();
        $location = $this->resolveLocation($validated['location']);

        $afbeeldingPath = $request->file('afbeelding')?->store('edities', 'public');

        $edition = Edition::query()->create([
            'naam' => $validated['naam'],
            'location_id' => $location->id,
            'omschrijving' => $validated['omschrijving'] ?? null,
            'afbeelding' => $afbeeldingPath,
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'] ?? null,
            'is_done' => (bool) ($validated['is_done'] ?? false),
        ]);

        $edition->load('location');

        $this->audit->log(
            actor: $actor,
            action: 'edition.created',
            subject: $edition,
            before: null,
            after: [
                'naam' => $edition->naam,
                'location_id' => $edition->location_id,
                'start_at' => $edition->start_at?->toIso8601String(),
                'end_at' => $edition->end_at?->toIso8601String(),
                'is_done' => $edition->is_done,
            ],
        );

        return new EditionResource($edition);
    }

    public function update(UpdateEditionRequest $request, Edition $edition): EditionResource
    {
        $this->authorize('update', $edition);

        /** @var User $actor */
        $actor = $request->user();

        /** @var array{naam?: string, location?: array{name: string, address: string, place: string, zipcode: string, osm_url?: string|null, instructions?: string|null}, omschrijving?: string|null, start_at?: string, end_at?: string|null, is_done?: bool, afbeelding_verwijderen?: bool} $validated */
        $validated = $request->validated();

        $before = [
            'naam' => $edition->naam,
            'location_id' => $edition->location_id,
            'omschrijving' => $edition->omschrijving,
            'start_at' => $edition->start_at?->toIso8601String(),
            'end_at' => $edition->end_at?->toIso8601String(),
            'is_done' => (bool) $edition->is_done,
        ];

        if (($validated['afbeelding_verwijderen'] ?? false) === true) {
            if (is_string($edition->afbeelding) && $edition->afbeelding !== '') {
                Storage::disk('public')->delete($edition->afbeelding);
            }

            $edition->afbeelding = null;
        }

        if ($request->hasFile('afbeelding')) {
            if (is_string($edition->afbeelding) && $edition->afbeelding !== '') {
                Storage::disk('public')->delete($edition->afbeelding);
            }

            $edition->afbeelding = $request->file('afbeelding')?->store('edities', 'public');
        }

        $startAt = $validated['start_at'] ?? $edition->start_at?->toIso8601String();
        $endAt = $validated['end_at'] ?? $edition->end_at?->toIso8601String();

        if (is_string($startAt) && is_string($endAt) && strtotime($endAt) < strtotime($startAt)) {
            throw ValidationException::withMessages([
                'end_at' => ['Einddatum moet gelijk aan of later dan startdatum zijn.'],
            ]);
        }

        if (array_key_exists('location', $validated) && is_array($validated['location'])) {
            $location = $this->resolveLocation($validated['location']);
            $validated['location_id'] = $location->id;
            unset($validated['location']);
        }

        $edition->fill($validated);
        $edition->save();
        $edition->load('location');

        $this->audit->log(
            actor: $actor,
            action: 'edition.updated',
            subject: $edition,
            before: $before,
            after: [
                'naam' => $edition->naam,
                'location_id' => $edition->location_id,
                'omschrijving' => $edition->omschrijving,
                'start_at' => $edition->start_at?->toIso8601String(),
                'end_at' => $edition->end_at?->toIso8601String(),
                'is_done' => (bool) $edition->is_done,
            ],
        );

        return new EditionResource($edition);
    }

    public function destroy(Request $request, Edition $edition): JsonResponse
    {
        $this->authorize('delete', $edition);

        /** @var User $actor */
        $actor = $request->user();

        if ($edition->teams()->exists() || $edition->competitionCategories()->exists()) {
            throw ValidationException::withMessages([
                'edition' => ['Deze editie kan niet verwijderd worden omdat er al teams of competitiegegevens aan gekoppeld zijn.'],
            ]);
        }

        $before = [
            'naam' => $edition->naam,
            'location_id' => $edition->location_id,
            'omschrijving' => $edition->omschrijving,
            'start_at' => $edition->start_at?->toIso8601String(),
            'end_at' => $edition->end_at?->toIso8601String(),
            'is_done' => (bool) $edition->is_done,
        ];

        if (is_string($edition->afbeelding) && $edition->afbeelding !== '') {
            Storage::disk('public')->delete($edition->afbeelding);
        }

        $edition->delete();

        $this->audit->log(
            actor: $actor,
            action: 'edition.deleted',
            subject: $edition,
            before: $before,
            after: null,
        );

        return response()->json([
            'message' => 'Editie verwijderd.',
        ]);
    }

    /**
     * @param  array{name: string, address: string, place: string, zipcode: string, osm_url?: string|null, instructions?: string|null}  $payload
     */
    private function resolveLocation(array $payload): Location
    {
        return Location::query()->firstOrCreate(
            [
                'name' => trim($payload['name']),
                'address' => trim($payload['address']),
                'place' => trim($payload['place']),
                'zipcode' => trim($payload['zipcode']),
                'osm_url' => isset($payload['osm_url'])
                    ? (trim((string) $payload['osm_url']) !== '' ? trim((string) $payload['osm_url']) : null)
                    : null,
                'instructions' => isset($payload['instructions'])
                    ? (trim((string) $payload['instructions']) !== '' ? trim((string) $payload['instructions']) : null)
                    : null,
            ],
        );
    }
}
