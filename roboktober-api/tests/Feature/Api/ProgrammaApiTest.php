<?php

declare(strict_types=1);

use App\Enums\ContentFormat;
use App\Enums\UserRole;
use App\Models\Edition;
use App\Models\ProgrammaItem;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('Programma API', function (): void {
    it('returns published programma items for an edition', function (): void {
        $edition = Edition::factory()->create([
            'is_done' => false,
        ]);

        ProgrammaItem::query()->create([
            'edition_id' => $edition->id,
            'titel' => 'Open workshop',
            'beschrijving' => '<p>Workshop content</p>',
            'content_format' => ContentFormat::Html,
            'start_at' => now()->addDay(),
            'end_at' => now()->addDay()->addHours(2),
            'volgorde' => 10,
            'is_published' => true,
        ]);

        ProgrammaItem::query()->create([
            'edition_id' => $edition->id,
            'titel' => 'Verborgen item',
            'beschrijving' => '<p>Niet publiek</p>',
            'content_format' => ContentFormat::Html,
            'start_at' => now()->addDays(2),
            'end_at' => now()->addDays(2)->addHours(1),
            'volgorde' => 20,
            'is_published' => false,
        ]);

        $this->getJson('/api/v1/edities/'.$edition->id.'/programma')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.titel', 'Open workshop');
    });

    it('allows moderators to manage programma items', function (): void {
        $edition = Edition::factory()->create([
            'is_done' => false,
        ]);

        $moderator = User::factory()->create([
            'role' => UserRole::Moderator,
        ]);

        $visitor = User::factory()->create([
            'role' => UserRole::Visitor,
        ]);

        $visitorToken = $visitor->createToken('visitor')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$visitorToken)
            ->postJson('/api/v1/admin/edities/'.$edition->id.'/programma', [
                'titel' => 'Geblokkeerd item',
                'beschrijving' => '<p>Geen rechten</p>',
                'content_format' => 'html',
                'start_at' => now()->toIso8601String(),
            ])
            ->assertForbidden();

        Sanctum::actingAs($moderator);

        $createResponse = $this
            ->postJson('/api/v1/admin/edities/'.$edition->id.'/programma', [
                'titel' => 'Keuring',
                'beschrijving' => '<p>Robot check en briefing</p>',
                'content_format' => 'html',
                'start_at' => now()->addDay()->toIso8601String(),
                'end_at' => now()->addDay()->addHour()->toIso8601String(),
                'volgorde' => 5,
                'is_published' => true,
            ])
            ->assertCreated()
            ->assertJsonPath('data.titel', 'Keuring');

        $itemId = (int) $createResponse->json('data.id');

        $this
            ->patchJson('/api/v1/admin/programma/'.$itemId, [
                'titel' => 'Technische keuring',
                'is_published' => false,
            ])
            ->assertOk()
            ->assertJsonPath('data.titel', 'Technische keuring')
            ->assertJsonPath('data.is_published', false);

        $this
            ->getJson('/api/v1/admin/edities/'.$edition->id.'/programma')
            ->assertOk()
            ->assertJsonFragment(['id' => $itemId]);

        $this
            ->deleteJson('/api/v1/admin/programma/'.$itemId)
            ->assertOk();

        $this->assertDatabaseMissing('programma_items', [
            'id' => $itemId,
        ]);
    });
});
