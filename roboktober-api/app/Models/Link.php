<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LinkCategorie;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Link model — external URL with optional cached OG metadata.
 *
 * Used on the Build Hub / Resources page.
 * Intentionally separate from Media (SOLID SRP: files ≠ URLs).
 *
 * @property int $id
 * @property string $titel
 * @property string $url
 * @property string|null $beschrijving
 * @property string|null $mime_type
 * @property array<string, mixed>|null $meta
 * @property LinkCategorie $categorie
 * @property string|null $eigenaar
 * @property Carbon|null $verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @see PLAN.md §5.2 — links schema
 */
class Link extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'titel',
        'url',
        'beschrijving',
        'mime_type',
        'meta',
        'categorie',
        'eigenaar',
        'verified_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'categorie' => LinkCategorie::class,
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Returns true if this link has been verified recently (within 30 days).
     */
    public function isGeverifieerd(): bool
    {
        return $this->verified_at !== null
            && $this->verified_at->isAfter(now()->subDays(30));
    }
}
