<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Mediable model — polymorphic pivot record linking media to any model.
 *
 * This model is rarely queried directly; use the HasMedia trait on content models.
 *
 * @property int $id
 * @property int $media_id
 * @property string $mediable_type
 * @property int $mediable_id
 * @property string $collectie
 * @property string|null $alt_tekst
 * @property string|null $onderschrift
 * @property int $volgorde
 * @property array<string, mixed>|null $meta
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @see PLAN.md §5.2 — mediables schema
 */
class Mediable extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'media_id',
        'mediable_type',
        'mediable_id',
        'collectie',
        'alt_tekst',
        'onderschrift',
        'volgorde',
        'meta',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'volgorde' => 'integer',
        ];
    }

    /**
     * The media file in this attachment.
     *
     * @return BelongsTo<Media, $this>
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * The owning model (Post, Robot, Page, Team, etc.).
     *
     * @return MorphTo<Model, $this>
     */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }
}
