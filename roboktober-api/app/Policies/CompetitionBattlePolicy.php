<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CompetitionBattle;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminRoles;

class CompetitionBattlePolicy
{
    use ChecksAdminRoles;

    public function update(User $user, CompetitionBattle $competitionBattle): bool
    {
        return $this->canModerate($user);
    }
}
