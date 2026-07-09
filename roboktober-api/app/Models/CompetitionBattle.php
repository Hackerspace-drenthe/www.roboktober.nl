<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionBattle extends Model
{
    /** @use HasFactory<\Database\Factories\CompetitionBattleFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'competition_category_id',
        'naam',
        'battle_mode',
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
     * @return BelongsTo<CompetitionCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CompetitionCategory::class, 'competition_category_id');
    }

    /**
     * @return HasMany<CompetitionBattleScore, $this>
     */
    public function scores(): HasMany
    {
        return $this->hasMany(CompetitionBattleScore::class)->orderByDesc('punten')->orderBy('id');
    }
}
