<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource transformer for Team.
 *
 * Email address is intentionally excluded from public API responses (OWASP A01: privacy).
 *
 * @mixin Team
 */
class TeamResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Team $team */
        $team = $this->resource;

        return [
            'id' => $team->id,
            'naam' => $team->naam,
            'beschrijving' => $team->opmerkingen,
            'edition' => new EditionResource($this->whenLoaded('edition')),
            'status' => $team->status->value,
            'status_label' => $team->status->label(),
            'captain_foto' => $this->whenLoaded(
                'captain',
                fn () => new MediaResource($team->captain?->mediaCollectie('foto')->first()),
                null,
            ),
            'captain' => [
                'naam' => $team->contactpersoon,
                'foto' => $this->whenLoaded(
                    'captain',
                    fn () => new MediaResource($team->captain?->mediaCollectie('foto')->first()),
                    null,
                ),
            ],
            'leden' => [
                'volwassenen' => $team->volwassenen,
                'kinderen' => $team->kinderen ?? 0,
                'totaal' => $team->volwassenen + ($team->kinderen ?? 0),
            ],
            'foto' => new MediaResource($this->whenLoaded('media', fn () => $this->mediaCollectie('foto')->first())),
            'leden_fotos' => MediaResource::collection($this->whenLoaded('media', fn () => $this->mediaCollectie('leden')->get())),
            'robots' => RobotResource::collection($this->whenLoaded('robots')),
            'updates' => TeamUpdateResource::collection($this->whenLoaded('updates')),
        ];
    }
}
