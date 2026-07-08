<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Team
 */
class AdminTeamResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'naam' => $this->naam,
            'contactpersoon' => $this->contactpersoon,
            'email' => $this->email,
            'edition_id' => $this->edition_id,
            'edition' => new EditionResource($this->whenLoaded('edition')),
            'volwassenen' => $this->volwassenen,
            'kinderen' => $this->kinderen,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'opmerkingen' => $this->opmerkingen,
            'captain_user_id' => $this->captain_user_id,
            'captain' => new AuthUserResource($this->whenLoaded('captain')),
            'foto' => new MediaResource($this->whenLoaded('media', fn () => $this->mediaCollectie('foto')->first())),
            'robots' => RobotResource::collection($this->whenLoaded('robots')),
            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),
        ];
    }
}
