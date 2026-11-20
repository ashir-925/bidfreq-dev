<?php

namespace App\Policies;

use App\Models\RepairRequest;
use App\Models\User;

class RepairRequestPolicy
{
    public function create(User $user): bool
    {
        return $user->role === 'customer';
    }

    public function view(User $user, RepairRequest $repair): bool
    {
        return $user->role === 'admin' || ($user->role === 'customer' && $user->id === $repair->user_id);
    }

    public function manage(User $user): bool
    {
        return $user->role === 'admin';
    }
}
