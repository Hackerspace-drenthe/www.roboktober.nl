<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Edition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Edition>
 */
class EditionFactory extends Factory
{
    protected $model = Edition::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('now', '+6 months');
        $end = (clone $start)->modify('+1 day');

        return [
            'naam' => 'Roboktober '.$start->format('Y'),
            'omschrijving' => fake()->sentence(16),
            'locatie' => 'Hackerspace Drenthe',
            'afbeelding' => null,
            'start_at' => $start,
            'end_at' => $end,
            'is_done' => false,
        ];
    }
}
