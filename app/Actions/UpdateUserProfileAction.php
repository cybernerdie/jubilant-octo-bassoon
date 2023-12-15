<?php

namespace App\Actions;

use App\Models\User;

class UpdateUserProfileAction
{
    /**
     * Update the user's profile data.
     *
     * @param \App\Models\User $user 
     * @param array $userData
     * @return \App\Models\User 
     */
    public function execute(User $user, array $userData): User
    {
        $user->profile()->update($userData);

        return $user;
    }
}

