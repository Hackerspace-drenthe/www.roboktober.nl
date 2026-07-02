<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasMedia;
use App\Enums\TeamStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Team model — combat robot team registration for Roboktober.
 *
 * Team photos are stored via the mediables 'foto' collection (uses HasMedia trait).
 * Registration is always open; organizer approves via status workflow.
 *
 * @property int $id
 * @property string $naam
 * @property string $contactpersoon
 * @property string $email
 * @property int $volwassenen
 * @property int|null $kinderen
 * @property TeamStatus $status
 * @property string|null $opmerkingen
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Robot> $robots
 *
 * @see PLAN.md §5.2 — teams schema
 */
class Team extends Model
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;
    use HasMedia;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'naam',
        'contactpersoon',
        'email',
        'volwassenen',
        'kinderen',
        'status',
        'opmerkingen',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TeamStatus::class,
            'volwassenen' => 'integer',
            'kinderen' => 'integer',
        ];
    }

    /**
     * All robots registered by this team.
     *
     * @return HasMany<Robot, $this>
     */
    public function robots(): HasMany
    {
        return $this->hasMany(Robot::class);
    }

    /**
     * Returns true if the team has been approved by an organizer.
     */
    public function isGoedgekeurd(): bool
    {
        return $this->status === TeamStatus::Approved;
    }
}
