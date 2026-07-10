<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Robot;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminRoles;

class RobotPolicy
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

    public function update(User $user, Robot $robot): bool
    {
        return $this->canModerate($user);
    }

    public function delete(User $user, Robot $robot): bool
    {
        return $this->canModerate($user);
    }
}
