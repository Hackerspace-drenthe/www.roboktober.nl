<?php

declare(strict_types=1);

use App\Models\Edition;

describe('GET /api/v1/edities', function (): void {
    it('returns only open editions', function (): void {
        $openEditie = Edition::factory()->create([
            'naam' => 'Roboktober 2027',
            'is_done' => false,
        ]);

        Edition::factory()->create([
            'naam' => 'Roboktober 2025',
            'is_done' => true,
        ]);

        $response = $this->getJson('/api/v1/edities');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $openEditie->id)
            ->assertJsonPath('data.0.naam', 'Roboktober 2027');
    });
});
