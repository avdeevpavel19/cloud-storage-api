<?php

namespace App\Services\Api\Auth;

use App\Models\User;

class RegisterService
{
    public function createUser(array $data): array
    {
        $user  = User::create($data);
        $token = $user->createToken('access_token')->plainTextToken;

        return [
            'id'    => $user['id'],
            'login' => $user['login'],
            'email' => $user['email'],
            'token' => $token
        ];
    }
}
