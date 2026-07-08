<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Visitor = 'visitor';
    case TeamCaptain = 'teamcaptain';
    case Moderator = 'moderator';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Visitor => 'Bezoeker',
            self::TeamCaptain => 'Teamcaptain',
            self::Moderator => 'Moderator',
            self::Admin => 'Admin',
        };
    }
}
