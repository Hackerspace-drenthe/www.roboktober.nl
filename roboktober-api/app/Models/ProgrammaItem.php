<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasMedia;
use App\Enums\ContentFormat;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $edition_id
 * @property string $titel
 * @property string|null $beschrijving
 * @property ContentFormat $content_format
 * @property Carbon $start_at
 * @property Carbon|null $end_at
 * @property int $volgorde
 * @property bool $is_published
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ProgrammaItem extends Model
{
    /** @use HasFactory<Factory<self>> */
    use HasFactory;
    use HasMedia;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'edition_id',
        'titel',
        'beschrijving',
        'content_format',
        'start_at',
        'end_at',
        'volgorde',
        'is_published',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content_format' => ContentFormat::class,
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'volgorde' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Edition, $this>
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }
}
