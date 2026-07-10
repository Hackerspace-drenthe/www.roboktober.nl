<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Edition;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminRoles;

class EditionPolicy
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

    public function update(User $user, Edition $edition): bool
    {
        return $this->canModerate($user);
    }

    public function delete(User $user, Edition $edition): bool
    {
        return $this->canModerate($user);
    }
}
