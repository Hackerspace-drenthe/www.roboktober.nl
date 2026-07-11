<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\TeamUpdate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TeamUpdate
 */
class AdminTeamUpdateResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'team_id' => $this->team_id,
            'team' => $this->whenLoaded('team', fn () => [
                'id' => $this->team->id,
                'naam' => $this->team->naam,
            ]),
            'titel' => $this->titel,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'content_format' => $this->content_format->value,
            'is_published' => $this->is_published,
            'published_at' => $this->published_at?->toISOString(),
            'afbeeldingen' => MediaResource::collection($this->whenLoaded('media', fn () => $this->mediaCollectie('gallery')->get())),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
