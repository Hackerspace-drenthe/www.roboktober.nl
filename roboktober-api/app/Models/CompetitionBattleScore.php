<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionBattleScore extends Model
{
    /** @use HasFactory<\Database\Factories\CompetitionBattleScoreFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'competition_battle_id',
        'robot_id',
        'punten',
        'opmerkingen',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'punten' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<CompetitionBattle, $this>
     */
    public function battle(): BelongsTo
    {
        return $this->belongsTo(CompetitionBattle::class, 'competition_battle_id');
    }

    /**
     * @return BelongsTo<Robot, $this>
     */
    public function robot(): BelongsTo
    {
        return $this->belongsTo(Robot::class);
    }
}
