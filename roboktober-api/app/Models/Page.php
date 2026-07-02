<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasMedia;
use App\Enums\ContentFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Page model — static CMS page managed via Filament admin.
 *
 * Hero background image: mediables 'hero' collection
 * Content format: HTML (Filament RichEditor) or Markdown
 *
 * @property int $id
 * @property string $slug
 * @property string $titel
 * @property string $content
 * @property ContentFormat $content_format
 * @property array<string, mixed>|null $seo
 * @property bool $is_published
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @see PLAN.md §5.2 — pages schema
 */
class Page extends Model
{
    use HasMedia;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'titel',
        'content',
        'content_format',
        'seo',
        'is_published',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content_format' => ContentFormat::class,
            'seo' => 'array',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }
}
