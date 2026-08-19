<?php

namespace App\Policies;

use App\Models\User;

class FormatPolicy
{
    /**
     * Create a new policy instance.
     */
    public function update(User $user): bool
    {
        return $user->is_admin && $user->environment_id === 0;
    }

    public function index(User $user): bool
    {
        return $user->is_admin && $user->environment_id === 0;
    }
}
