<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Gewichtsklasse;
use App\Enums\RobotStatus;
use App\Models\Robot;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Robot>
 */
class RobotFactory extends Factory
{
    protected $model = Robot::class;

    public function definition(): array
    {
        $gewichtsklasse = $this->faker->randomElement(Gewichtsklasse::cases());

        return [
            'team_id' => Team::factory(),
            'naam' => $this->faker->word().'Bot',
            'gewichtsklasse' => $gewichtsklasse instanceof Gewichtsklasse ? $gewichtsklasse->value : Gewichtsklasse::Antweight->value,
            'beschrijving' => $this->faker->optional()->sentence(),
            'status' => RobotStatus::InOntwikkeling,
        ];
    }
}
