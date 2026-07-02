<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Link categories for the Build Hub / Resources page.
 *
 * @see PLAN.md §5.2 — links.categorie
 * @see PLAN.md §6.5  — Build Hub page design
 */
enum LinkCategorie: string
{
    case Wallie = 'wallie';
    case Community = 'community';
    case Competitie = 'competitie';
    case Tools = 'tools';
    case Onderdelen = 'onderdelen';
    case Documentatie = 'documentatie';

    public function label(): string
    {
        return match ($this) {
            self::Wallie => 'Wallie',
            self::Community => 'Community',
            self::Competitie => 'Competitie',
            self::Tools => 'Tools',
            self::Onderdelen => 'Onderdelen',
            self::Documentatie => 'Documentatie',
        };
    }
}
