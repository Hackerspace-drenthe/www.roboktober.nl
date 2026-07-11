<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CompetitionBattle;
use App\Models\CompetitionCategory;
use App\Models\Edition;
use App\Models\Robot;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class CompetitionController extends Controller
{
    public function leaderboard(Edition $edition): JsonResponse
    {
        $categories = CompetitionCategory::query()
            ->where('edition_id', $edition->id)
            ->with([
                'battles.scores.robot.team',
            ])
            ->orderBy('volgorde')
            ->orderBy('id')
            ->get();

        /** @var array<int, array{robot: Robot, punten: int}> $overallTotals */
        $overallTotals = [];

        /** @var list<array<string, mixed>> $categoryPayload */
        $categoryPayload = [];

        foreach ($categories as $category) {
            /** @var array<int, array{robot: Robot, punten: int}> $categoryTotals */
            $categoryTotals = [];

            foreach ($category->battles as $battle) {
                foreach ($battle->scores as $score) {
                    $robot = $score->robot;

                    if (! $robot instanceof Robot) {
                        continue;
                    }

                    $robotId = $robot->id;

                    if (! isset($categoryTotals[$robotId])) {
                        $categoryTotals[$robotId] = [
                            'robot' => $robot,
                            'punten' => 0,
                        ];
                    }

                    if (! isset($overallTotals[$robotId])) {
                        $overallTotals[$robotId] = [
                            'robot' => $robot,
                            'punten' => 0,
                        ];
                    }

                    $categoryTotals[$robotId]['punten'] += (int) $score->punten;
                    $overallTotals[$robotId]['punten'] += (int) $score->punten;
                }
            }

            $ranking = $this->mapRanking(collect(array_values($categoryTotals)));

            /** @var Collection<int, CompetitionBattle> $sortedBattles */
            $sortedBattles = $category->battles->sort(static function (CompetitionBattle $a, CompetitionBattle $b): int {
                $aScheduled = $a->scheduled_at?->getTimestamp() ?? PHP_INT_MAX;
                $bScheduled = $b->scheduled_at?->getTimestamp() ?? PHP_INT_MAX;
                $byDate = $aScheduled <=> $bScheduled;

                if ($byDate !== 0) {
                    return $byDate;
                }

                $byOrder = $a->volgorde <=> $b->volgorde;
                if ($byOrder !== 0) {
                    return $byOrder;
                }

                return $a->id <=> $b->id;
            });

            $categoryPayload[] = [
                'id' => $category->id,
                'naam' => $category->naam,
                'slug' => $category->slug,
                'omschrijving' => $category->omschrijving,
                'volgorde' => $category->volgorde,
                'battles_count' => $category->battles->count(),
                'battles' => $sortedBattles
                    ->values()
                    ->map(static fn (CompetitionBattle $battle): array => [
                        'id' => $battle->id,
                        'naam' => $battle->naam,
                        'battle_mode' => $battle->battle_mode,
                        'scheduled_at' => $battle->scheduled_at?->toIso8601String(),
                    ]),
                'winner' => $ranking[0] ?? null,
                'ranking' => $ranking,
            ];
        }

        return response()->json([
            'data' => [
                'edition' => [
                    'id' => $edition->id,
                    'naam' => $edition->naam,
                    'start_at' => $edition->start_at->toIso8601String(),
                    'end_at' => $edition->end_at?->toIso8601String(),
                ],
                'categories' => $categoryPayload,
                'overall' => $this->mapRanking(collect(array_values($overallTotals))),
            ],
        ]);
    }

    /**
     * @param  Collection<int, array{robot: Robot, punten: int}>  $totals
     * @return list<array<string, mixed>>
     */
    private function mapRanking(Collection $totals): array
    {
        /** @var list<array{robot: Robot, punten: int}> $rows */
        $rows = array_values($totals->all());

        usort($rows, static function (array $a, array $b): int {
            $byPoints = $b['punten'] <=> $a['punten'];
            if ($byPoints !== 0) {
                return $byPoints;
            }

            return strcmp(mb_strtolower($a['robot']->naam), mb_strtolower($b['robot']->naam));
        });

        $ranking = [];

        foreach ($rows as $index => $row) {
            $robot = $row['robot'];

            $ranking[] = [
                'positie' => $index + 1,
                'punten' => (int) $row['punten'],
                'robot' => [
                    'id' => $robot->id,
                    'naam' => $robot->naam,
                    'status' => $robot->status->value,
                    'team' => $robot->team !== null ? [
                        'id' => $robot->team->id,
                        'naam' => $robot->team->naam,
                    ] : null,
                ],
            ];
        }

        return $ranking;
    }
}
