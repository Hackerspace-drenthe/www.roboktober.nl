<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Edition extends Model
{
    /** @use HasFactory<\Database\Factories\EditionFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'naam',
        'omschrijving',
        'location_id',
        'afbeelding',
        'start_at',
        'end_at',
        'is_done',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'is_done' => 'boolean',
        ];
    }

    /**
     * @return HasMany<Team, $this>
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * @return BelongsTo<Location, $this>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return HasMany<CompetitionCategory, $this>
     */
    public function competitionCategories(): HasMany
    {
        return $this->hasMany(CompetitionCategory::class)->orderBy('volgorde')->orderBy('id');
    }
}
