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
        return [
            'id' => $this->id,
            'naam' => $this->naam,
            'beschrijving' => $this->opmerkingen,
            'edition' => new EditionResource($this->whenLoaded('edition')),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'captain' => [
                'naam' => $this->contactpersoon,
            ],
            'leden' => [
                'volwassenen' => $this->volwassenen,
                'kinderen' => $this->kinderen ?? 0,
                'totaal' => $this->volwassenen + ($this->kinderen ?? 0),
            ],
            'foto' => new MediaResource($this->whenLoaded('media', fn () => $this->mediaCollectie('foto')->first())),
            'captain_foto' => new MediaResource($this->whenLoaded('media', fn () => $this->mediaCollectie('captain')->first())),
            'leden_fotos' => MediaResource::collection($this->whenLoaded('media', fn () => $this->mediaCollectie('leden')->get())),
            'robots' => RobotResource::collection($this->whenLoaded('robots')),
            'updates' => TeamUpdateResource::collection($this->whenLoaded('updates')),
        ];
    }
}
