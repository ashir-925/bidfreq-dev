<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        return $user->role === 'admin' || ($user->role === 'customer' && $user->id === (int) $project->user_id);
    }

    public function manage(User $user): bool
    {
        return $user->role === 'admin';
    }
}
