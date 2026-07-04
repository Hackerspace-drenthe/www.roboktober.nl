<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Gewichtsklasse;
use App\Enums\RobotStatus;
use App\Enums\TeamStatus;
use App\Models\Edition;
use App\Models\Robot;
use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $edition = Edition::query()->where('is_done', false)->orderBy('start_at')->first();

        $team = Team::create([
            'edition_id' => $edition?->id,
            'naam' => 'Hackerspace Drenthe',
            'contactpersoon' => 'Hackerspace Drenthe',
            'email' => 'info@hackerspacedrenthe.nl',
            'volwassenen' => 3,
            'status' => TeamStatus::Approved,
        ]);

        Robot::create([
            'team_id' => $team->id,
            'naam' => 'HackerBot',
            'gewichtsklasse' => Gewichtsklasse::Antweight,
            'beschrijving' => 'HackerBot is de strijdende vertegenwoordiger van Hackerspace Drenthe. Een compacte antweight (max. 150 gram) opgebouwd uit aluminium en 3D-geprinte onderdelen. Nog volop in ontwikkeling — elke woensdagavond een stap verder.',
            'status' => RobotStatus::InOntwikkeling,
        ]);
    }
}
