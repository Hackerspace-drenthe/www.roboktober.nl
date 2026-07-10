<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Enums\ContentFormat;
use App\Models\ProgrammaItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * @mixin ProgrammaItem
 */
class ProgrammaItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $renderedBeschrijving = $this->content_format === ContentFormat::Markdown
            ? Str::markdown($this->beschrijving)
            : $this->beschrijving;

        return [
            'id' => $this->id,
            'edition_id' => $this->edition_id,
            'titel' => $this->titel,
            'beschrijving' => $this->beschrijving,
            'beschrijving_rendered' => $renderedBeschrijving,
            'content_format' => $this->content_format->value,
            'start_at' => $this->start_at?->toIso8601String(),
            'end_at' => $this->end_at?->toIso8601String(),
            'volgorde' => $this->volgorde,
            'is_published' => $this->is_published,
            'gallery' => MediaResource::collection($this->whenLoaded('media', fn () => $this->mediaCollectie('gallery')->get())),
            'bijlagen' => MediaResource::collection($this->whenLoaded('media', fn () => $this->mediaCollectie('bijlagen')->get())),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
