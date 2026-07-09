<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageVisitAggregate extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'page_path',
        'bucket_start',
        'visits',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'bucket_start' => 'datetime',
            'visits' => 'integer',
        ];
    }
}
