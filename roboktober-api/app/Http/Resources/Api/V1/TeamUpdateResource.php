<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\TeamUpdate;
use App\Services\Security\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TeamUpdate
 */
class TeamUpdateResource extends JsonResource
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
            'titel' => $this->titel,
            'excerpt' => $this->excerpt,
            'content' => $content,
            'content_format' => $this->content_format->value,
            'published_at' => $this->published_at?->toIso8601String(),
            'afbeeldingen' => MediaResource::collection($this->whenLoaded(
                'media',
                fn () => $this->mediaCollectie('gallery')->get(),
            )),
        ];
    }
}
