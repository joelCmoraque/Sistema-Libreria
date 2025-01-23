<?php

namespace App\Policies;

use App\Models\User;

class WelcomePolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user)
    {
        //
        return $user->hasRole(['invitado']);
    }
}
