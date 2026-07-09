<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\CompetitionBattle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CompetitionBattle
 */
class CompetitionBattleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'competition_category_id' => $this->competition_category_id,
            'naam' => $this->naam,
            'battle_mode' => $this->battle_mode,
            'omschrijving' => $this->omschrijving,
            'volgorde' => $this->volgorde,
            'scores' => CompetitionBattleScoreResource::collection($this->whenLoaded('scores')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
