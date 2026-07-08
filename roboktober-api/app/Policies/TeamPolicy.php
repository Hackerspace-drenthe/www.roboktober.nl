<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function viewAdminIndex(User $user): bool
    {
        return $user->hasAnyRole(UserRole::Moderator, UserRole::Admin);
    }

    public function viewAdmin(User $user, Team $team): bool
    {
        return $user->hasAnyRole(UserRole::Moderator, UserRole::Admin);
    }

    public function moderate(User $user, Team $team): bool
    {
        return $user->hasAnyRole(UserRole::Moderator, UserRole::Admin);
    }

    public function viewOwn(User $user, Team $team): bool
    {
        return $user->hasAnyRole(UserRole::Moderator, UserRole::Admin)
            || $team->captain_user_id === $user->id;
    }
}
