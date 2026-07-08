<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Media
 */
class RichMediaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $url = $this->url();

        return [
            'id' => $this->id,
            'naam' => $this->naam,
            'url' => $url,
            'mime_type' => $this->mime_type,
            'extensie' => $this->extensie,
            'grootte' => $this->grootte,
            'downloads' => $this->downloads,
            'created_at' => $this->created_at?->toISOString(),
            'html_snippet' => $this->htmlSnippet($url),
            'markdown_snippet' => $this->markdownSnippet($url),
        ];
    }

    private function htmlSnippet(string $url): string
    {
        if ($this->isAfbeelding()) {
            return '<img src="'.$url.'" alt="" loading="lazy" />';
        }

        if (str_starts_with($this->mime_type, 'video/')) {
            return '<video controls preload="metadata" src="'.$url.'"></video>';
        }

        if ($this->is3dModel()) {
            return '<a href="'.$url.'" download>Download STL/3D-bestand</a>';
        }

        return '<a href="'.$url.'" target="_blank" rel="noopener">'.$this->bestandsnaam.'</a>';
    }

    private function markdownSnippet(string $url): string
    {
        if ($this->isAfbeelding()) {
            return '![]('.$url.')';
        }

        if (str_starts_with($this->mime_type, 'video/')) {
            return '[Bekijk video]('.$url.')';
        }

        if ($this->is3dModel()) {
            return '[Download STL/3D-bestand]('.$url.')';
        }

        return '['.$this->bestandsnaam.']('.$url.')';
    }
}
