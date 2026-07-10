<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use App\Policies\Concerns\ChecksAdminRoles;

class PostPolicy
{
    use ChecksAdminRoles;

    public function viewAny(User $user): bool
    {
        return $this->canModerate($user);
    }

    public function view(User $user, Post $post): bool
    {
        return $this->canModerate($user);
    }

    public function update(User $user, Post $post): bool
    {
        return $this->canModerate($user);
    }
}
