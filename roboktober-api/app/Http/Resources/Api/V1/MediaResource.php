<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Media;
use App\Models\Mediable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource transformer for Media.
 *
 * Returns only the data needed by the frontend, never exposes internal paths.
 * URL is generated via asset() helper to support CDN/proxy setups.
 *
 * @mixin Media
 *
 * @property Mediable $pivot Custom pivot model with alt_tekst, onderschrift, volgorde columns
 */
class MediaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'naam' => $this->naam,
            'url' => $this->url(),
            'mime_type' => $this->mime_type,
            'extensie' => $this->extensie,
            'grootte' => $this->grootte,
            'meta' => $this->meta,
            'versie' => $this->versie,
            'downloads' => $this->downloads,
            // Alt text and caption come from the mediables pivot (context-dependent)
            'alt_tekst' => $this->whenPivotLoaded('mediables', fn () => $this->pivot->alt_tekst),
            'onderschrift' => $this->whenPivotLoaded('mediables', fn () => $this->pivot->onderschrift),
            'volgorde' => $this->whenPivotLoaded('mediables', fn () => $this->pivot->volgorde),
        ];
    }
}
