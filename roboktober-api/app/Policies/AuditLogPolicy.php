<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksAdminRoles;

class AuditLogPolicy
{
    use ChecksAdminRoles;

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }
}
