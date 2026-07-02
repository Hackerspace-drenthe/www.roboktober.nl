<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Team registration status workflow.
 *
 * pending  → Team submitted registration, awaiting organizer review
 * approved → Organizer confirmed the team; they can enter robots
 * rejected → Team cannot participate (capacity, safety, etc.)
 *
 * @see PLAN.md §5.2 — teams.status
 */
enum TeamStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'In behandeling',
            self::Approved => 'Goedgekeurd',
            self::Rejected => 'Afgewezen',
        };
    }

    public function kleur(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }
}
