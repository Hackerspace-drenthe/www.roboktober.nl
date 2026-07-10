<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Page;
use App\Services\Security\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource transformer for Page.
 *
 * @mixin Page
 */
class PageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $content = $this->content_format->value === 'html'
            ? HtmlSanitizer::sanitize($this->content)
            : $this->content;

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'titel' => $this->titel,
            'content' => $content,
            'content_format' => $this->content_format->value,
            'seo' => $this->seo,
            'published_at' => $this->published_at?->toIso8601String(),
            'hero' => new MediaResource($this->whenLoaded('media', fn () => $this->featuredMedia())),
        ];
    }
}
