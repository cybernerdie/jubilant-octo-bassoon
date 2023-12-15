<?php

namespace App\Actions;

use App\Models\User;
use App\Models\UserProfile;

class CreateUserProfileAction
{
    /**
     * Create a profile for a user.
     *
     * @param  \App\Models\User  $user
     * @return \App\Models\UserProfile
     */
    public function execute(User $user): UserProfile
    {
        return $user->profile()->create();
    }
}
