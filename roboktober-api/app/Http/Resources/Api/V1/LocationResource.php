<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Location
 */
class LocationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'place' => $this->place,
            'zipcode' => $this->zipcode,
            'osm_url' => $this->osm_url,
            'instructions' => $this->instructions,
            'full_address' => $this->fullAddress(),
        ];
    }
}
