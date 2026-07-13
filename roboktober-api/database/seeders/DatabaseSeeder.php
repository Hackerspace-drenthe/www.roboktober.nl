<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@hackerspace-drenthe.nl'],
            [
                'name' => 'Roboktober Admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
            ],
        );

        $this->call([
            EditionSeeder::class,
            ProgrammaItemSeeder::class,
            TeamSeeder::class,
            LinkSeeder::class,
            PostSeeder::class,
        ]);
    }
}
