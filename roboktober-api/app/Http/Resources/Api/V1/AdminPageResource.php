<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Page
 */
class AdminPageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'titel' => $this->titel,
            'content' => $this->content,
            'content_format' => $this->content_format->value,
            'seo' => $this->seo,
            'is_published' => $this->is_published,
            'published_at' => $this->published_at?->toISOString(),
            'hero' => new MediaResource($this->whenLoaded('media', fn () => $this->featuredMedia())),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
