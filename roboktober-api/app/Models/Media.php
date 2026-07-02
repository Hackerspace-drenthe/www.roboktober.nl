<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Media model — central file/asset storage for Roboktober.
 *
 * Represents any uploadable file: images, STL models, PDFs, BOM files, firmware, etc.
 * Linked to content models via the mediables polymorphic pivot table.
 *
 * @property int $id
 * @property string $naam
 * @property string $bestandsnaam
 * @property string $pad
 * @property string $disk
 * @property string $mime_type
 * @property string $extensie
 * @property int $grootte
 * @property string|null $hash
 * @property array<string, mixed>|null $meta
 * @property string|null $versie
 * @property string|null $versie_notities
 * @property int|null $vorige_versie_id
 * @property int $downloads
 * @property int|null $geupload_door
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @see PLAN.md §5.2 — media schema
 */
class Media extends Model
{
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'naam',
        'bestandsnaam',
        'pad',
        'disk',
        'mime_type',
        'extensie',
        'grootte',
        'hash',
        'meta',
        'versie',
        'versie_notities',
        'vorige_versie_id',
        'downloads',
        'geupload_door',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'grootte' => 'integer',
            'downloads' => 'integer',
        ];
    }

    /**
     * The user who uploaded this file.
     *
     * @return BelongsTo<User, $this>
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'geupload_door');
    }

    /**
     * The previous version of this file (version chain).
     *
     * @return BelongsTo<Media, $this>
     */
    public function vorigeVersie(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'vorige_versie_id');
    }

    /**
     * Generated thumbnail/resized variants of this file.
     *
     * @return HasMany<MediaVariant, $this>
     */
    public function varianten(): HasMany
    {
        return $this->hasMany(MediaVariant::class);
    }

    /**
     * Returns the public URL for this media file.
     */
    public function url(): string
    {
        return asset('storage/'.$this->pad);
    }

    /**
     * Returns true if this file is an image (can be displayed in <img> tags).
     */
    public function isAfbeelding(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Returns true if this file is a 3D model (STL, OBJ, etc.).
     */
    public function is3dModel(): bool
    {
        return in_array($this->mime_type, ['model/stl', 'model/obj', 'application/sla'], true);
    }
}
