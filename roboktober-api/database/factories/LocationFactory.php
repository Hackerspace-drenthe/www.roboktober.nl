<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Hackerspace Drenthe',
            'address' => 'Industrieweg 12',
            'place' => 'Assen',
            'zipcode' => '9403 AB',
            'instructions' => $this->faker->sentence(12),
        ];
    }
}
