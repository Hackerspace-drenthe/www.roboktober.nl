<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $edition_id
 * @property string $naam
 * @property string $slug
 * @property string|null $omschrijving
 * @property int $volgorde
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, CompetitionBattle> $battles
 */
class CompetitionCategory extends Model
{
    /** @use HasFactory<Factory<self>> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'edition_id',
        'naam',
        'slug',
        'omschrijving',
        'volgorde',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'volgorde' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Edition, $this>
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }

    /**
     * @return HasMany<CompetitionBattle, $this>
     */
    public function battles(): HasMany
    {
        $relation = $this->hasMany(CompetitionBattle::class);
        $relation->orderBy('volgorde')->orderBy('id');

        return $relation;
    }
}
