<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Post;
use App\Services\Security\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource transformer for Post.
 *
 * @mixin Post
 */
class PostResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $content = $this->content;

        if ($this->content_format->value === 'html') {
            $content = HtmlSanitizer::sanitize($this->content);
        }

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'titel' => $this->titel,
            'excerpt' => $this->excerpt,
            'content' => $this->when(
                $request->routeIs('api.v1.posts.show'),
                fn () => $content,
            ),
            'content_format' => $this->content_format,
            'categorie' => $this->categorie,
            'tags' => $this->tags ?? [],
            'published_at' => $this->published_at?->toIso8601String(),
            // Media collections
            'featured_image' => new MediaResource($this->whenLoaded('media', fn () => $this->featuredMedia())),
            'gallery' => MediaResource::collection($this->whenLoaded(
                'media',
                fn () => $this->mediaCollectie('gallery')->get(),
            )),
            'bijlagen' => MediaResource::collection($this->whenLoaded(
                'media',
                fn () => $this->mediaCollectie('bijlagen')->get(),
            )),
        ];
    }
}
