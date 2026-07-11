<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CompetitionCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionCategory extends Model
{
    /** @use HasFactory<CompetitionCategoryFactory> */
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
        return $this->hasMany(CompetitionBattle::class)->orderBy('volgorde')->orderBy('id');
    }
}
