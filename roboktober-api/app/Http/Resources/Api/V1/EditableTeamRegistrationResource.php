<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Team
 */
class EditableTeamRegistrationResource extends JsonResource
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
            'contactpersoon' => $this->contactpersoon,
            'email' => $this->email,
            'volwassenen' => $this->volwassenen,
            'kinderen' => $this->kinderen,
            'opmerkingen' => $this->opmerkingen,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'foto' => new MediaResource($this->whenLoaded('media', fn () => $this->mediaCollectie('foto')->first())),
            'robots' => $this->robots->map(static function ($robot): array {
                return [
                    'id' => $robot->id,
                    'naam' => $robot->naam,
                    'gewichtsklasse' => $robot->gewichtsklasse->value,
                    'gewichtsklasse_label' => $robot->gewichtsklasse->label(),
                    'beschrijving' => $robot->beschrijving,
                ];
            })->values(),
        ];
    }
}
