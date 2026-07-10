<?php

declare(strict_types=1);

namespace App\Policies\Concerns;

use App\Enums\UserRole;
use App\Models\User;

trait ChecksAdminRoles
{
    protected function canModerate(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Moderator, UserRole::Admin);
    }

    protected function isAdmin(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Admin);
    }
}
