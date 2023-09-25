<?php

namespace App\Services\Api\Auth;

use Illuminate\Support\Facades\Auth;

class LoginService
{
    /**
     * @param array $userData Ассоциативный массив с данными пользователя (логин и пароль).
     *
     * @return array Массив, содержащий информацию о пользователе и токен доступа в случае успешной аутентификации,
     * или пустой массив в случае неудачи.
     */
    public function loginUser(array $userData)
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
