<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasMedia;
use App\Enums\ContentFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgrammaItem extends Model
{
    use HasMedia;
    use HasFactory;

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
