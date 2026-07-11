<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateAdminTeamUpdateContentRequest;
use App\Http\Requests\Api\V1\UpdatePublishStateRequest;
use App\Http\Resources\Api\V1\AdminTeamUpdateResource;
use App\Models\TeamUpdate;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamUpdateModerationController extends Controller
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', TeamUpdate::class);

        $status = trim(request()->string('status')->toString());
        $zoekterm = trim(request()->string('q')->toString());

        $updates = TeamUpdate::query()
            ->when($status === 'published', static fn ($query) => $query->where('is_published', true))
            ->when($status === 'draft', static fn ($query) => $query->where('is_published', false))
            ->when($zoekterm !== '', static fn ($query) => $query->where('titel', 'like', '%'.$zoekterm.'%'))
            ->with(['team', 'media'])
            ->latest('published_at')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return AdminTeamUpdateResource::collection($updates);
    }

    public function show(TeamUpdate $teamUpdate): AdminTeamUpdateResource
    {
        $this->authorize('view', $teamUpdate);

        $teamUpdate->load(['team', 'media']);

        return new AdminTeamUpdateResource($teamUpdate);
    }

    public function updateStatus(UpdatePublishStateRequest $request, TeamUpdate $teamUpdate): AdminTeamUpdateResource
    {
        $this->authorize('update', $teamUpdate);

        /** @var User $actor */
        $actor = $request->user();

        /** @var array{is_published: bool, published_at?: string|null} $validated */
        $validated = $request->validated();

        $isPublished = (bool) $validated['is_published'];

        $before = [
            'is_published' => (bool) $teamUpdate->is_published,
            'published_at' => $teamUpdate->published_at?->toISOString(),
        ];

        $teamUpdate->forceFill([
            'is_published' => $isPublished,
            'published_at' => $isPublished
                ? ($validated['published_at'] ?? $teamUpdate->published_at ?? now())
                : null,
        ])->save();

        $this->audit->log(
            actor: $actor,
            action: 'team_update.publish_state_updated',
            subject: $teamUpdate,
            before: $before,
            after: [
                'is_published' => (bool) $teamUpdate->is_published,
                'published_at' => $teamUpdate->published_at?->toISOString(),
            ],
        );

        $teamUpdate->load(['team', 'media']);

        return new AdminTeamUpdateResource($teamUpdate);
    }

    public function updateContent(UpdateAdminTeamUpdateContentRequest $request, TeamUpdate $teamUpdate): AdminTeamUpdateResource
    {
        $this->authorize('update', $teamUpdate);

        /** @var User $actor */
        $actor = $request->user();

        /** @var array{titel: string, excerpt?: string|null, content: string, content_format: string} $validated */
        $validated = $request->validated();

        $before = [
            'titel' => $teamUpdate->titel,
            'excerpt' => $teamUpdate->excerpt,
            'content' => $teamUpdate->content,
            'content_format' => $teamUpdate->content_format->value,
        ];

        $teamUpdate->forceFill([
            'titel' => $validated['titel'],
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'content_format' => $validated['content_format'],
        ])->save();

        $this->audit->log(
            actor: $actor,
            action: 'team_update.content_updated',
            subject: $teamUpdate,
            before: $before,
            after: [
                'titel' => $teamUpdate->titel,
                'excerpt' => $teamUpdate->excerpt,
                'content' => $teamUpdate->content,
                'content_format' => $teamUpdate->content_format->value,
            ],
        );

        $teamUpdate->load(['team', 'media']);

        return new AdminTeamUpdateResource($teamUpdate);
    }
}
