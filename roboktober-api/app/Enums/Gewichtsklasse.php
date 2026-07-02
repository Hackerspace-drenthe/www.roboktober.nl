<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Combat robot weight classes for Roboktober.
 *
 * Antweight:     up to 150 grams
 * Beetleweight:  up to 1.36 kg (3 lbs)
 * Featherweight: up to 13.6 kg (30 lbs)
 *
 * @see PLAN.md §5.1 — gewichtsklassen
 */
enum Gewichtsklasse: string
{
    case Antweight = 'antweight';
    case Beetleweight = 'beetleweight';
    case Featherweight = 'featherweight';

    public function label(): string
    {
        return match ($this) {
            self::Antweight => 'Antweight (max. 150 g)',
            self::Beetleweight => 'Beetleweight (max. 1,36 kg)',
            self::Featherweight => 'Featherweight (max. 13,6 kg)',
        };
    }

    public function maxGewichtGram(): int
    {
        return match ($this) {
            self::Antweight => 150,
            self::Beetleweight => 1360,
            self::Featherweight => 13600,
        };
    }
}
