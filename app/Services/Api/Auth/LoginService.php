<?php

namespace App\Services\Api\Auth;

use Illuminate\Support\Facades\Auth;

class LoginService
{
    public function loginUser(array $userData): array
    {
        if (Auth::attempt($userData)) {
            $user  = Auth::user();
            $token = $user->createToken('access_token')->plainTextToken;

            return [
                'user'  => [
                    'id'    => $user->id,
                    'login' => $user->login,
                ],
                'token' => $token
            ];
        }

        return [];
    }
}
