<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\TeamMembershipStatus;
use App\Enums\TeamStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ReviewTeamMembershipRequest;
use App\Http\Requests\Api\V1\StoreTeamMembershipRequest;
use App\Http\Resources\Api\V1\TeamMembershipResource;
use App\Models\Team;
use App\Models\TeamMembership;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class TeamMembershipController extends Controller
{
    public function apply(StoreTeamMembershipRequest $request, Team $team): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($team->status === TeamStatus::Rejected) {
            return response()->json([
                'message' => 'Dit team is afgewezen en accepteert geen lidmaatschapsaanvragen.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($team->captain_user_id === $user->id) {
            return response()->json([
                'message' => 'Je bent al captain van dit team.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $membership = TeamMembership::query()->firstOrNew([
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);

        if ($membership->exists && $membership->status === TeamMembershipStatus::Approved) {
            return response()->json([
                'message' => 'Je bent al goedgekeurd als teamlid.',
            ], Response::HTTP_CONFLICT);
        }

        $created = ! $membership->exists;

        $membership->forceFill([
            'status' => TeamMembershipStatus::Pending,
            'request_message' => (string) ($request->validated()['request_message'] ?? ''),
            'reviewed_at' => null,
            'reviewed_by' => null,
        ])->save();

        $membership->load(['team', 'user']);

        return response()->json([
            'message' => $created ? 'Aanvraag verstuurd naar de teamcaptain.' : 'Aanvraag bijgewerkt.',
            'data' => new TeamMembershipResource($membership),
        ], $created ? Response::HTTP_CREATED : Response::HTTP_OK);
    }

    public function myMemberships(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        $items = TeamMembership::query()
            ->where('user_id', $user->id)
            ->with(['team', 'user'])
            ->latest('id')
            ->get();

        return TeamMembershipResource::collection($items);
    }

    public function captainRequests(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        $items = TeamMembership::query()
            ->where('status', TeamMembershipStatus::Pending)
            ->whereHas('team', static function ($query) use ($user): void {
                $query->where('captain_user_id', $user->id);
            })
            ->with(['team', 'user'])
            ->oldest('id')
            ->get();

        return TeamMembershipResource::collection($items);
    }

    public function review(ReviewTeamMembershipRequest $request, TeamMembership $teamMembership): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $teamMembership->load('team');

        $isCaptainOfTeam = $teamMembership->team?->captain_user_id === $user->id;
        $isPrivileged = $user->hasAnyRole(UserRole::Admin, UserRole::Moderator);

        if (! $isCaptainOfTeam && ! $isPrivileged) {
            return response()->json([
                'message' => 'Alleen de captain of moderatie mag aanvragen beoordelen.',
            ], Response::HTTP_FORBIDDEN);
        }

        $status = TeamMembershipStatus::from((string) $request->validated()['status']);

        $teamMembership->forceFill([
            'status' => $status,
            'reviewed_at' => now(),
            'reviewed_by' => $user->id,
        ])->save();

        $teamMembership->load(['team', 'user']);

        return response()->json([
            'message' => 'Aanvraagstatus bijgewerkt.',
            'data' => new TeamMembershipResource($teamMembership),
        ], Response::HTTP_OK);
    }
}
