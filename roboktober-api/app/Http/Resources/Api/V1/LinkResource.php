<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource transformer for Link.
 *
 * @mixin Link
 */
class LinkResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titel' => $this->titel,
            'url' => $this->url,
            'beschrijving' => $this->beschrijving,
            'categorie' => $this->categorie->value,
            'categorie_label' => $this->categorie->label(),
            'eigenaar' => $this->eigenaar,
            'og_image' => $this->meta['og_image'] ?? null,
            'favicon' => $this->meta['favicon'] ?? null,
            'verified_at' => $this->verified_at?->toIso8601String(),
        ];
    }
}
