<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $competition_battle_id
 * @property int $robot_id
 * @property int $punten
 * @property string|null $opmerkingen
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CompetitionBattle|null $battle
 * @property-read Robot|null $robot
 */
class CompetitionBattleScore extends Model
{
    /** @use HasFactory<Factory<self>> */
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
