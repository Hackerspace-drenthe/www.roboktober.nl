<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\TeamStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateTeamStatusRequest;
use App\Http\Resources\Api\V1\AdminTeamResource;
use App\Models\Team;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamModerationController extends Controller
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAdminIndex', Team::class);

        $status = request()->query('status');
        $zoekterm = request()->query('q');

        $teams = Team::query()
            ->when(is_string($status) && $status !== '', static function ($query) use ($status): void {
                $query->where('status', $status);
            })
            ->when(is_string($zoekterm) && $zoekterm !== '', static function ($query) use ($zoekterm): void {
                $query
                    ->where('naam', 'like', '%'.$zoekterm.'%')
                    ->orWhere('contactpersoon', 'like', '%'.$zoekterm.'%')
                    ->orWhere('email', 'like', '%'.$zoekterm.'%');
            })
            ->with(['edition', 'captain', 'media', 'robots'])
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return AdminTeamResource::collection($teams);
    }

    public function show(Team $team): AdminTeamResource
    {
        $this->authorize('viewAdmin', $team);

        $team->load(['edition', 'captain', 'media', 'robots.media']);

        return new AdminTeamResource($team);
    }

    public function updateStatus(UpdateTeamStatusRequest $request, Team $team): AdminTeamResource
    {
        $this->authorize('moderate', $team);

        /** @var User $actor */
        $actor = $request->user();

        /** @var array{status: string, opmerkingen?: string|null} $validated */
        $validated = $request->validated();

        $before = [
            'status' => $team->status->value,
            'opmerkingen' => $team->opmerkingen,
        ];

        $team->fill([
            'status' => TeamStatus::from($validated['status']),
            'opmerkingen' => $validated['opmerkingen'] ?? $team->opmerkingen,
        ]);
        $team->save();

        $this->audit->log(
            actor: $actor,
            action: 'team.status_updated',
            subject: $team,
            before: $before,
            after: [
                'status' => $team->status->value,
                'opmerkingen' => $team->opmerkingen,
            ],
        );

        $team->load(['edition', 'captain', 'media', 'robots.media']);

        return new AdminTeamResource($team);
    }
}
