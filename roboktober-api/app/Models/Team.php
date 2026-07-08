<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasMedia;
use App\Enums\TeamStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Team model — combat robot team registration for Roboktober.
 *
 * Team photos are stored via the mediables 'foto' collection (uses HasMedia trait).
 * Registration is always open; organizer approves via status workflow.
 *
 * @property int $id
 * @property int|null $edition_id
 * @property string $naam
 * @property string $contactpersoon
 * @property string $email
 * @property int $volwassenen
 * @property int|null $kinderen
 * @property TeamStatus $status
 * @property string|null $opmerkingen
 * @property int|null $captain_user_id
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
        'edition_id',
        'naam',
        'contactpersoon',
        'email',
        'volwassenen',
        'kinderen',
        'status',
        'opmerkingen',
        'captain_user_id',
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
     * The edition this team registered for.
     *
     * @return BelongsTo<Edition, $this>
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }

    /**
     * Authenticated team captain account for this team.
     *
     * @return BelongsTo<User, $this>
     */
    public function captain(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captain_user_id');
    }

    /**
     * Progress updates posted by this team.
     *
     * @return HasMany<TeamUpdate, $this>
     */
    public function updates(): HasMany
    {
        return $this->hasMany(TeamUpdate::class);
    }

    /**
     * Team membership requests/members for this team.
     *
     * @return HasMany<TeamMembership, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(TeamMembership::class);
    }

    /**
     * Returns true if the team has been approved by an organizer.
     */
    public function isGoedgekeurd(): bool
    {
        return $this->status === TeamStatus::Approved;
    }
}
