<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TeamStatus;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'naam' => $this->faker->company() . ' Robotics',
            'contactpersoon' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'volwassenen' => $this->faker->numberBetween(1, 4),
            'kinderen' => $this->faker->optional(0.3)->numberBetween(1, 3),
            'status' => TeamStatus::Pending,
            'opmerkingen' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(['status' => TeamStatus::Approved]);
    }

    public function pending(): static
    {
        return $this->state(['status' => TeamStatus::Pending]);
    }
}
