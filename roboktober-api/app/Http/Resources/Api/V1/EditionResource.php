<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Edition;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Edition
 */
class EditionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var FilesystemAdapter $publicDisk */
        $publicDisk = Storage::disk('public');

        return [
            'id' => $this->id,
            'naam' => $this->naam,
            'omschrijving' => $this->omschrijving,
            'location' => new LocationResource($this->whenLoaded('location')),
            'afbeelding_url' => is_string($this->afbeelding)
                ? $publicDisk->url($this->afbeelding)
                : null,
            'start_at' => $this->start_at->toIso8601String(),
            'end_at' => $this->end_at?->toIso8601String(),
            'is_done' => $this->is_done,
        ];
    }
}
