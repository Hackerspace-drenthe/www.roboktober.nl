<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ContentFormat;
use App\Models\Edition;
use App\Models\ProgrammaItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ProgrammaItemSeeder extends Seeder
{
    public function run(): void
    {
        $edition = Edition::query()->where('is_done', false)->orderBy('start_at')->first();

        if (! $edition) {
            return;
        }

        $items = [
            [
                'titel' => 'Kickoff en kennismaking',
                'beschrijving' => '<p>We trappen Roboktober af met uitleg over combat robotics, veiligheidsregels en de arena.</p><p>Nieuwe deelnemers krijgen een korte rondleiding en tips voor de eerste build.</p>',
                'content_format' => ContentFormat::Html,
                'start_at' => Carbon::create(2026, 10, 1, 19, 0, 0),
                'end_at' => Carbon::create(2026, 10, 1, 21, 30, 0),
                'volgorde' => 10,
                'is_published' => true,
            ],
            [
                'titel' => 'Workshop: antweight bouwen',
                'beschrijving' => '<p>Praktische workshop met onderdelenkeuze, aandrijving en basiselektronica.</p><p>Neem je eigen project mee of start met een nieuwe robot.</p>',
                'content_format' => ContentFormat::Html,
                'start_at' => Carbon::create(2026, 10, 8, 19, 0, 0),
                'end_at' => Carbon::create(2026, 10, 8, 22, 0, 0),
                'volgorde' => 20,
                'is_published' => true,
            ],
            [
                'titel' => 'Technische keuring',
                'beschrijving' => '<p>Alle robots worden gecontroleerd op gewicht, failsafe en veiligheid voordat ze de arena in gaan.</p>',
                'content_format' => ContentFormat::Html,
                'start_at' => Carbon::create(2026, 10, 31, 12, 0, 0),
                'end_at' => Carbon::create(2026, 10, 31, 14, 0, 0),
                'volgorde' => 30,
                'is_published' => true,
            ],
            [
                'titel' => 'Battle Day',
                'beschrijving' => '<p>De gevechten beginnen. Volg de klassementen en moedig je favoriete team aan.</p><p>Publiek is welkom.</p>',
                'content_format' => ContentFormat::Html,
                'start_at' => Carbon::create(2026, 10, 31, 14, 0, 0),
                'end_at' => Carbon::create(2026, 10, 31, 21, 30, 0),
                'volgorde' => 40,
                'is_published' => true,
            ],
        ];

        foreach ($items as $item) {
            ProgrammaItem::query()->updateOrCreate(
                [
                    'edition_id' => $edition->id,
                    'titel' => $item['titel'],
                    'start_at' => $item['start_at'],
                ],
                [
                    'beschrijving' => $item['beschrijving'],
                    'content_format' => $item['content_format'],
                    'end_at' => $item['end_at'],
                    'volgorde' => $item['volgorde'],
                    'is_published' => $item['is_published'],
                ]
            );
        }
    }
}
