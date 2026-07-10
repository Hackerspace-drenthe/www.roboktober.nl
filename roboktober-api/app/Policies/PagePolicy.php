<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Page;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminRoles;

class PagePolicy
{
    use ChecksAdminRoles;

    public function viewAny(User $user): bool
    {
        return $this->canModerate($user);
    }

    public function view(User $user, Page $page): bool
    {
        return $this->canModerate($user);
    }

    public function update(User $user, Page $page): bool
    {
        return $this->canModerate($user);
    }
}
