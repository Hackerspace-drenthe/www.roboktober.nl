<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * RobotVote model — a user's 1-10 awesomeness vote for a robot.
 *
 * @property int $id
 * @property int $robot_id
 * @property int $user_id
 * @property int $stars
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class RobotVote extends Model
{
    /** @use HasFactory<Factory<self>> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'robot_id',
        'user_id',
        'stars',
    ];

    /**
     * @return BelongsTo<Robot, $this>
     */
    public function robot(): BelongsTo
    {
        return $this->belongsTo(Robot::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
