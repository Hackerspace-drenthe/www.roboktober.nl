<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * BattleRegistration model — robot entry for a specific battle event date.
 *
 * Records technical inspection status and organizer approval per robot per date.
 *
 * @property int $id
 * @property int $robot_id
 * @property Carbon $datum
 * @property bool $technische_check
 * @property bool $approved
 * @property string|null $opmerkingen
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @see PLAN.md §5.2 — battle_registrations schema
 */
class BattleRegistration extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'robot_id',
        'datum',
        'technische_check',
        'approved',
        'opmerkingen',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'datum' => 'date',
            'technische_check' => 'boolean',
            'approved' => 'boolean',
        ];
    }

    /**
     * The robot registered for this battle.
     *
     * @return BelongsTo<Robot, $this>
     */
    public function robot(): BelongsTo
    {
        return $this->belongsTo(Robot::class);
    }

    /**
     * Returns true if this registration is cleared for battle.
     */
    public function isKlaarVoorBattle(): bool
    {
        return $this->technische_check && $this->approved;
    }
}
