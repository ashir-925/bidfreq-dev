<?php

namespace App\Policies;

use App\Models\User;

class LibraryTaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' && $user->status === 'active';
    }

    public function manage(User $user): bool
    {
        return $this->viewAny($user);
    }
}
