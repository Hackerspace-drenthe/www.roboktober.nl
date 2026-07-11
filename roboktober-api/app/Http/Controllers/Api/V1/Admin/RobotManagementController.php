<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAdminRobotRequest;
use App\Http\Requests\Api\V1\UpdateAdminRobotRequest;
use App\Http\Resources\Api\V1\AdminRobotResource;
use App\Models\Robot;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class RobotManagementController extends Controller
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Robot::class);

        $status = request()->query('status');
        $gewichtsklasse = request()->query('gewichtsklasse');
        $zoekterm = request()->query('q');

        $robots = Robot::query()
            ->when(is_string($status) && $status !== '', static fn ($query) => $query->where('status', $status))
            ->when(is_string($gewichtsklasse) && $gewichtsklasse !== '', static fn ($query) => $query->where('gewichtsklasse', $gewichtsklasse))
            ->when(is_string($zoekterm) && $zoekterm !== '', static function ($query) use ($zoekterm): void {
                $query
                    ->where('naam', 'like', '%'.$zoekterm.'%')
                    ->orWhereHas('team', static function ($teamQuery) use ($zoekterm): void {
                        $teamQuery->where('naam', 'like', '%'.$zoekterm.'%');
                    });
            })
            ->with('team')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return AdminRobotResource::collection($robots);
    }

    public function store(StoreAdminRobotRequest $request): AdminRobotResource
    {
        $this->authorize('create', Robot::class);

        /** @var User $actor */
        $actor = $request->user();

        /** @var array{team_id: int, naam: string, gewichtsklasse: string, status: string, beschrijving?: string|null} $validated */
        $validated = $request->validated();

        $robot = Robot::query()->create([
            'team_id' => (int) $validated['team_id'],
            'naam' => $validated['naam'],
            'gewichtsklasse' => $validated['gewichtsklasse'],
            'status' => $validated['status'],
            'beschrijving' => $validated['beschrijving'] ?? null,
            'awesomeness_score' => 0,
            'awesomeness_votes_count' => 0,
        ]);

        $this->audit->log(
            actor: $actor,
            action: 'robot.created',
            subject: $robot,
            before: null,
            after: [
                'team_id' => $robot->team_id,
                'naam' => $robot->naam,
                'gewichtsklasse' => $robot->gewichtsklasse->value,
                'status' => $robot->status->value,
            ],
        );

        $robot->load('team');

        return new AdminRobotResource($robot);
    }

    public function update(UpdateAdminRobotRequest $request, Robot $robot): AdminRobotResource
    {
        $this->authorize('update', $robot);

        /** @var User $actor */
        $actor = $request->user();

        /** @var array{team_id?: int, naam?: string, gewichtsklasse?: string, status?: string, beschrijving?: string|null} $validated */
        $validated = $request->validated();

        $before = [
            'team_id' => $robot->team_id,
            'naam' => $robot->naam,
            'gewichtsklasse' => $robot->gewichtsklasse->value,
            'status' => $robot->status->value,
            'beschrijving' => $robot->beschrijving,
        ];

        $robot->fill($validated);
        $robot->save();

        $this->audit->log(
            actor: $actor,
            action: 'robot.updated',
            subject: $robot,
            before: $before,
            after: [
                'team_id' => $robot->team_id,
                'naam' => $robot->naam,
                'gewichtsklasse' => $robot->gewichtsklasse->value,
                'status' => $robot->status->value,
                'beschrijving' => $robot->beschrijving,
            ],
        );

        $robot->load('team');

        return new AdminRobotResource($robot);
    }

    public function destroy(Request $request, Robot $robot): JsonResponse
    {
        $this->authorize('delete', $robot);

        /** @var User $actor */
        $actor = $request->user();

        if ($robot->competitionScores()->exists()) {
            throw ValidationException::withMessages([
                'robot' => ['Robot kan niet verwijderd worden omdat er competitiepunten aan gekoppeld zijn.'],
            ]);
        }

        $before = [
            'team_id' => $robot->team_id,
            'naam' => $robot->naam,
            'gewichtsklasse' => $robot->gewichtsklasse->value,
            'status' => $robot->status->value,
            'beschrijving' => $robot->beschrijving,
        ];

        $robot->delete();

        $this->audit->log(
            actor: $actor,
            action: 'robot.deleted',
            subject: $robot,
            before: $before,
            after: null,
        );

        return response()->json([
            'message' => 'Robot verwijderd.',
        ]);
    }
}
