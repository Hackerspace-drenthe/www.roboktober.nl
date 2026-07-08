<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\RobotResource;
use App\Http\Resources\Api\V1\TeamResource;
use App\Models\Team;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * REST API controller for approved teams and their robots.
 *
 * Only approved teams are visible on the public API.
 *
 * Routes:
 * - GET /api/v1/teams       → TeamController@index
 * - GET /api/v1/teams/{id}  → TeamController@show
 *
 * @see PLAN.md §5.2 — REST API endpoints
 */
class TeamController extends Controller
{
    /**
     * Returns all approved teams with their robots.
     */
    public function index(): AnonymousResourceCollection
    {
        $teams = Team::query()
            ->where('status', 'approved')
            ->with(['edition', 'media', 'robots.media'])
            ->orderBy('naam')
            ->get();

        return TeamResource::collection($teams);
    }

    /**
     * Returns a single approved team by ID.
     */
    public function show(int $id): TeamResource
    {
        $team = Team::query()
            ->where('id', $id)
            ->where('status', 'approved')
            ->with([
                'edition',
                'media',
                'robots.media',
                'updates' => static function ($query): void {
                    $query
                        ->where('is_published', true)
                        ->with('media')
                        ->latest('published_at')
                        ->latest('id');
                },
            ])
            ->firstOrFail();

        return new TeamResource($team);
    }

    /**
     * Returns all robots of a single approved team.
     */
    public function robots(int $id): AnonymousResourceCollection
    {
        $team = Team::query()
            ->where('id', $id)
            ->where('status', 'approved')
            ->with(['robots.media'])
            ->firstOrFail();

        return RobotResource::collection($team->robots);
    }
}
