<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Robot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Robot
 */
class AdminRobotResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'team_id' => $this->team_id,
            'team' => $this->team !== null ? [
                'id' => $this->team->id,
                'naam' => $this->team->naam,
            ] : null,
            'naam' => $this->naam,
            'gewichtsklasse' => $this->gewichtsklasse->value,
            'gewichtsklasse_label' => $this->gewichtsklasse->label(),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'beschrijving' => $this->beschrijving,
            'awesomeness_score' => (float) $this->awesomeness_score,
            'awesomeness_votes_count' => (int) $this->awesomeness_votes_count,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
