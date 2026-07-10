<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CompetitionCategory;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminRoles;

class CompetitionCategoryPolicy
{
    use ChecksAdminRoles;

    public function viewAny(User $user): bool
    {
        return $this->canModerate($user);
    }

    public function create(User $user): bool
    {
        return $this->canModerate($user);
    }

    public function update(User $user, CompetitionCategory $competitionCategory): bool
    {
        return $this->canModerate($user);
    }
}
