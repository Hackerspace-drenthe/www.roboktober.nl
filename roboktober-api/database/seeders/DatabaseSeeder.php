<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Roboktober Admin',
            'email' => 'admin@hackerspacedrenthe.nl',
        ]);

        $this->call([
            EditionSeeder::class,
            TeamSeeder::class,
            LinkSeeder::class,
            PostSeeder::class,
        ]);
    }
}
