<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Auth\Access\Response;

class UserProfilePolicy
{
    /**
     * Determine if the user is authorized to upload a CV.
     *
     * @param  \App\Models\User  $user
     * @param \App\Models\UserProfile  $profile
     * @return bool
     */
    public function uploadCV(User $user, UserProfile $profile): bool
    {
        return $user->id === $profile->user_id;
    }
}
