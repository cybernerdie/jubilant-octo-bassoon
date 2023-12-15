<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterUserAction
{
    /**
     * Execute user registration.
     *
     * @param  array $userData
     * @return User
     */
    public function execute(array $userData): User
    {
        if (isset($userData['password'])) {
            $userData['password'] = Hash::make($userData['password']);
        }

        return User::create($userData);
    }
}
