<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasMedia;
use App\Enums\ContentFormat;
use Database\Factories\TeamUpdateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamUpdate extends Model
{
    /** @use HasFactory<TeamUpdateFactory> */
    use HasFactory;
    use HasMedia;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'team_id',
        'titel',
        'excerpt',
        'content',
        'content_format',
        'is_published',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content_format' => ContentFormat::class,
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
