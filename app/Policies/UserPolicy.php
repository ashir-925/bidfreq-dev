<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function changeStatus(User $user, User $target): bool
    {
        return $user->role === 'admin' && $target->role !== 'super_admin' && ! $user->is($target);
    }
}
