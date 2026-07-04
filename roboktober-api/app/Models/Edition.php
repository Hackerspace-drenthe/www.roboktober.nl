<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'locatie',
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
}
