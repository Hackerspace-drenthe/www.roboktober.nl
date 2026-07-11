<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * MediaVariant model — auto-generated thumbnail and resized variants of a media file.
 *
 * Generated asynchronously by a queue job after upload.
 * Variants are named: 'thumb_sm' (150px), 'medium' (600px), 'large' (1200px), 'preview'.
 *
 * @property int $id
 * @property int $media_id
 * @property string $naam
 * @property string $pad
 * @property string $mime_type
 * @property int $grootte
 * @property array<string, mixed>|null $meta
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @see PLAN.md §5.2 — media_varianten schema
 */
class MediaVariant extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'media_id',
        'naam',
        'pad',
        'mime_type',
        'grootte',
        'meta',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'grootte' => 'integer',
        ];
    }

    /**
     * The source media file this variant was generated from.
     *
     * @return BelongsTo<Media, $this>
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * Returns the public URL for this variant.
     */
    public function url(): string
    {
        $media = $this->media()->first();
        $disk = $media instanceof Media ? $media->disk : 'public';

        /** @var FilesystemAdapter $filesystem */
        $filesystem = Storage::disk($disk);

        return $filesystem->url($this->pad);
    }
}
