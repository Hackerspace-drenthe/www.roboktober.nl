<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Post
 */
class AdminPostResource extends JsonResource
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
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'content_format' => $this->content_format->value,
            'categorie' => $this->categorie,
            'tags' => $this->tags ?? [],
            'is_published' => $this->is_published,
            'published_at' => $this->published_at?->toISOString(),
            'featured_image' => new MediaResource($this->whenLoaded('media', fn () => $this->featuredMedia())),
            'gallery' => MediaResource::collection($this->whenLoaded('media', fn () => $this->mediaCollectie('gallery')->get())),
            'bijlagen' => MediaResource::collection($this->whenLoaded('media', fn () => $this->mediaCollectie('bijlagen')->get())),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
