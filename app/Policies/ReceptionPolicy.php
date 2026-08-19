<?php

namespace App\Policies;

use App\Models\User;


class ReceptionPolicy
{

    public function destroy(User $user): bool
    {
        return $user->is_admin && $user->environment_id === 0;
    }
}
