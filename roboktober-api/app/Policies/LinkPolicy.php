<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Link;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminRoles;

class LinkPolicy
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

    public function update(User $user, Link $link): bool
    {
        return $this->canModerate($user);
    }

    public function delete(User $user, Link $link): bool
    {
        return $this->canModerate($user);
    }
}
