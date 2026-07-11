<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasMedia;
use App\Enums\ContentFormat;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Post model — news/blog article for the Nieuws section.
 *
 * Media collections:
 * - 'featured'  → single header/cover image
 * - 'gallery'   → additional in-article photos
 * - 'bijlagen'  → downloadable attachments (PDF, STL, etc.)
 *
 * @property int $id
 * @property string $slug
 * @property string $titel
 * @property string|null $excerpt
 * @property string $content
 * @property ContentFormat $content_format
 * @property string|null $categorie
 * @property list<string>|null $tags
 * @property bool $is_published
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @see PLAN.md §5.2 — posts schema
 * @see PLAN.md §6.7  — Nieuws/Blog page design
 */
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;
    use HasMedia;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'titel',
        'excerpt',
        'content',
        'content_format',
        'categorie',
        'tags',
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
            'tags' => 'array',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Returns the URL-friendly slug-based route key.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
