<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Robot build/readiness status.
 *
 * in_ontwikkeling → Robot is under construction
 * gereed          → Robot is finished and tested
 * battle_ready    → Robot has passed technical inspection and is cleared for battle
 *
 * @see PLAN.md §5.2 — robots.status
 */
enum RobotStatus: string
{
    case InOntwikkeling = 'in_ontwikkeling';
    case Gereed = 'gereed';
    case BattleReady = 'battle_ready';

    public function label(): string
    {
        return match ($this) {
            self::InOntwikkeling => 'In ontwikkeling',
            self::Gereed => 'Gereed',
            self::BattleReady => 'Battle ready',
        };
    }

    public function kleur(): string
    {
        return match ($this) {
            self::InOntwikkeling => 'gray',
            self::Gereed => 'info',
            self::BattleReady => 'success',
        };
    }
}
