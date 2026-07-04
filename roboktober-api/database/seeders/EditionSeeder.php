<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Edition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class EditionSeeder extends Seeder
{
    public function run(): void
    {
        Edition::query()->updateOrCreate(
            ['naam' => 'Roboktober 2026'],
            [
                'omschrijving' => 'Eerste Roboktober editie met focus op antweight combat robots.',
                'locatie' => 'Hackerspace Drenthe',
                'start_at' => Carbon::create(2026, 10, 1, 19, 0, 0),
                'end_at' => Carbon::create(2026, 10, 31, 22, 0, 0),
                'is_done' => false,
            ]
        );
    }
}
