<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns teams list', function (): void {
    $response = $this->getJson('/api/v1/teams');

    $response->assertStatus(200);
});
