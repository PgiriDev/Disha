<?php

namespace App\Policies\Concerns;

use App\Models\User;

/**
 * Shared authorization rules for administration-managed resources.
 *
 * Only users with the administrator role may manage airport operations data.
 */
trait AdminOnlyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, mixed $model): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, mixed $model): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, mixed $model): bool
    {
        return $user->isAdmin();
    }
}
