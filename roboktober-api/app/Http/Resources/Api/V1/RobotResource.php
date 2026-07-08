<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Robot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource transformer for Robot.
 *
 * @mixin Robot
 */
class RobotResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'naam' => $this->naam,
            'gewichtsklasse' => $this->gewichtsklasse->value,
            'gewichtsklasse_label' => $this->gewichtsklasse->label(),
            'beschrijving' => $this->beschrijving,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'awesomeness_score' => (float) $this->awesomeness_score,
            'awesomeness_votes_count' => (int) $this->awesomeness_votes_count,
            'team' => new TeamResource($this->whenLoaded('team')),
            'foto' => new MediaResource($this->whenLoaded('media', fn () => $this->mediaCollectie('foto')->first())),
            'bijlagen' => MediaResource::collection($this->whenLoaded(
                'media',
                fn () => $this->mediaCollectie('bijlagen')->get(),
            )),
        ];
    }
}
