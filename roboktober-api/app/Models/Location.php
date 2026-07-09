<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    /** @use HasFactory<\Database\Factories\LocationFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'address',
        'place',
        'zipcode',
        'osm_url',
        'instructions',
    ];

    /**
     * @return HasMany<Edition, $this>
     */
    public function editions(): HasMany
    {
        return $this->hasMany(Edition::class);
    }

    public function fullAddress(): string
    {
        $parts = array_filter([
            $this->name,
            $this->address,
            $this->zipcode,
            $this->place,
        ], static fn (?string $part): bool => is_string($part) && trim($part) !== '');

        return implode(', ', $parts);
    }
}
