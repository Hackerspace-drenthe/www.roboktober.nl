<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\TeamStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreRobotVoteRequest;
use App\Models\Robot;
use App\Models\RobotVote;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RobotVoteController extends Controller
{
    public function store(StoreRobotVoteRequest $request, Robot $robot): JsonResponse
    {
        /** @var array{stars: int} $validated */
        $validated = $request->validated();

        $robot->loadMissing('team');

        if ($robot->team?->status !== TeamStatus::Approved) {
            return response()->json([
                'message' => 'Robot niet gevonden.',
            ], Response::HTTP_NOT_FOUND);
        }

        /** @var User $user */
        $user = $request->user();

        $vote = RobotVote::query()->firstOrNew([
            'robot_id' => $robot->id,
            'user_id' => $user->id,
        ]);

        $created = ! $vote->exists;
        $vote->stars = $validated['stars'];
        $vote->save();

        $stats = RobotVote::query()
            ->where('robot_id', $robot->id)
            ->selectRaw('COUNT(*) as votes_count, AVG(stars) as average_stars')
            ->first();

        if ($stats === null) {
            $votesCount = 0;
            $averageStars = 0.0;
        } else {
            $votesCount = (int) $stats->votes_count;
            $averageStars = (float) $stats->average_stars;
        }

        $robot->forceFill([
            'awesomeness_votes_count' => $votesCount,
            'awesomeness_score' => round($averageStars, 2),
        ])->save();

        return response()->json([
            'message' => $created ? 'Stem opgeslagen.' : 'Stem bijgewerkt.',
            'data' => [
                'robot_id' => $robot->id,
                'my_stars' => $vote->stars,
                'awesomeness_score' => (float) $robot->awesomeness_score,
                'awesomeness_votes_count' => (int) $robot->awesomeness_votes_count,
            ],
        ], $created ? Response::HTTP_CREATED : Response::HTTP_OK);
    }
}
