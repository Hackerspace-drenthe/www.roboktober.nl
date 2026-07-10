<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\TeamUpdate;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminRoles;

class TeamUpdatePolicy
{
    use ChecksAdminRoles;

    public function viewAny(User $user): bool
    {
        return $this->canModerate($user);
    }

    public function view(User $user, TeamUpdate $teamUpdate): bool
    {
        return $this->canModerate($user);
    }

    public function update(User $user, TeamUpdate $teamUpdate): bool
    {
        return $this->canModerate($user);
    }
}
