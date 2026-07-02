<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasMedia;
use App\Enums\Gewichtsklasse;
use App\Enums\RobotStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Robot model — combat robot registered for Roboktober events.
 *
 * Photos: mediables 'foto' collection
 * STL/CAD files: mediables 'bijlagen' collection
 *
 * @property int $id
 * @property int $team_id
 * @property string $naam
 * @property Gewichtsklasse $gewichtsklasse
 * @property string|null $beschrijving
 * @property RobotStatus $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @see PLAN.md §5.2 — robots schema
 */
class Robot extends Model
{
    /** @use HasFactory<\Database\Factories\RobotFactory> */
    use HasFactory;
    use HasMedia;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'team_id',
        'naam',
        'gewichtsklasse',
        'beschrijving',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gewichtsklasse' => Gewichtsklasse::class,
            'status' => RobotStatus::class,
        ];
    }

    /**
     * The team that built this robot.
     *
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Battle registrations for this robot.
     *
     * @return HasMany<BattleRegistration, $this>
     */
    public function battleRegistraties(): HasMany
    {
        return $this->hasMany(BattleRegistration::class);
    }

    /**
     * Returns true if this robot has passed technical inspection and is cleared.
     */
    public function isBattleReady(): bool
    {
        return $this->status === RobotStatus::BattleReady;
    }
}
