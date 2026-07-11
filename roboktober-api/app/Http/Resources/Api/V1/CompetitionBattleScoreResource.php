<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\CompetitionBattleScore;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CompetitionBattleScore
 */
class CompetitionBattleScoreResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var CompetitionBattleScore $score */
        $score = $this->resource;
        $robot = $score->robot;

        return [
            'id' => $score->id,
            'punten' => $score->punten,
            'opmerkingen' => $score->opmerkingen,
            'robot' => $robot !== null ? [
                'id' => $robot->id,
                'naam' => $robot->naam,
                'status' => $robot->status->value,
                'team' => $robot->team !== null ? [
                    'id' => $robot->team->id,
                    'naam' => $robot->team->naam,
                ] : null,
            ] : null,
            'created_at' => $score->created_at?->toISOString(),
            'updated_at' => $score->updated_at?->toISOString(),
        ];
    }
}
