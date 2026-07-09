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
        return [
            'id' => $this->id,
            'punten' => $this->punten,
            'opmerkingen' => $this->opmerkingen,
            'robot' => $this->robot !== null ? [
                'id' => $this->robot->id,
                'naam' => $this->robot->naam,
                'status' => $this->robot->status->value,
                'team' => $this->robot->team !== null ? [
                    'id' => $this->robot->team->id,
                    'naam' => $this->robot->team->naam,
                ] : null,
            ] : null,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
