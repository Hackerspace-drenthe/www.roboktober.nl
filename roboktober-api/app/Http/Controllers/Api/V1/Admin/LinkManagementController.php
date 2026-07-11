<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAdminLinkRequest;
use App\Http\Requests\Api\V1\UpdateAdminLinkRequest;
use App\Http\Resources\Api\V1\AdminLinkResource;
use App\Models\Link;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LinkManagementController extends Controller
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Link::class);

        $categorie = trim(request()->string('categorie')->toString());
        $zoekterm = trim(request()->string('q')->toString());

        $links = Link::query()
            ->when($categorie !== '', static fn ($query) => $query->where('categorie', $categorie))
            ->when($zoekterm !== '', static fn ($query) => $query
                ->where('titel', 'like', '%'.$zoekterm.'%')
                ->orWhere('url', 'like', '%'.$zoekterm.'%')
                ->orWhere('eigenaar', 'like', '%'.$zoekterm.'%'))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return AdminLinkResource::collection($links);
    }

    public function store(StoreAdminLinkRequest $request): AdminLinkResource
    {
        $this->authorize('create', Link::class);

        /** @var User $actor */
        $actor = $request->user();

        /** @var array{titel: string, url: string, beschrijving?: string|null, categorie: string, eigenaar?: string|null, verified_at?: string|null} $validated */
        $validated = $request->validated();

        $link = Link::query()->create([
            'titel' => $validated['titel'],
            'url' => $validated['url'],
            'beschrijving' => $validated['beschrijving'] ?? null,
            'categorie' => $validated['categorie'],
            'eigenaar' => $validated['eigenaar'] ?? null,
            'verified_at' => $validated['verified_at'] ?? null,
        ]);

        $this->audit->log(
            actor: $actor,
            action: 'link.created',
            subject: $link,
            before: null,
            after: [
                'titel' => $link->titel,
                'url' => $link->url,
                'categorie' => $link->categorie->value,
            ],
        );

        return new AdminLinkResource($link);
    }

    public function update(UpdateAdminLinkRequest $request, Link $link): AdminLinkResource
    {
        $this->authorize('update', $link);

        /** @var User $actor */
        $actor = $request->user();

        /** @var array{titel?: string, url?: string, beschrijving?: string|null, categorie?: string, eigenaar?: string|null, verified_at?: string|null} $validated */
        $validated = $request->validated();

        $before = [
            'titel' => $link->titel,
            'url' => $link->url,
            'beschrijving' => $link->beschrijving,
            'categorie' => $link->categorie->value,
            'eigenaar' => $link->eigenaar,
            'verified_at' => $link->verified_at?->toIso8601String(),
        ];

        $link->fill($validated);
        $link->save();

        $this->audit->log(
            actor: $actor,
            action: 'link.updated',
            subject: $link,
            before: $before,
            after: [
                'titel' => $link->titel,
                'url' => $link->url,
                'beschrijving' => $link->beschrijving,
                'categorie' => $link->categorie->value,
                'eigenaar' => $link->eigenaar,
                'verified_at' => $link->verified_at?->toIso8601String(),
            ],
        );

        return new AdminLinkResource($link);
    }

    public function destroy(Request $request, Link $link): JsonResponse
    {
        $this->authorize('delete', $link);

        /** @var User $actor */
        $actor = $request->user();

        $before = [
            'titel' => $link->titel,
            'url' => $link->url,
            'beschrijving' => $link->beschrijving,
            'categorie' => $link->categorie->value,
            'eigenaar' => $link->eigenaar,
            'verified_at' => $link->verified_at?->toIso8601String(),
        ];

        $link->delete();

        $this->audit->log(
            actor: $actor,
            action: 'link.deleted',
            subject: $link,
            before: $before,
            after: null,
        );

        return response()->json([
            'message' => 'Link verwijderd.',
        ]);
    }
}
