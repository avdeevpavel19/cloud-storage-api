<?php

namespace App\Services\Api\Auth;

use App\Models\User;

class RegisterService
{
    private function returnData(User $data, string $token): array
    {
        return [
            'id'    => $data['id'],
            'login' => $data['login'],
            'email' => $data['email'],
            'token' => $token
        ];
    }

    public function createUser(array $data): array
    {
        $user     = User::create($data);
        $token    = $user->createToken('access_token')->plainTextToken;
        $userData = $this->returnData($user, $token);

        return $userData;
    }
}
