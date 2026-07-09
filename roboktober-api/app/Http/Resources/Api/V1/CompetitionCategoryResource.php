<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\CompetitionCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CompetitionCategory
 */
class CompetitionCategoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'edition_id' => $this->edition_id,
            'naam' => $this->naam,
            'slug' => $this->slug,
            'omschrijving' => $this->omschrijving,
            'volgorde' => $this->volgorde,
            'battles' => CompetitionBattleResource::collection($this->whenLoaded('battles')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
