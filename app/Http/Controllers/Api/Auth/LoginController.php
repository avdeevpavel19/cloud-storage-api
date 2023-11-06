<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\BaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Services\Api\Auth\LoginService;
use App\Traits\HttpResponse;

class LoginController extends Controller
{
    use HttpResponse;

    public function store(LoginRequest $request, LoginService $service)
    {
        try {
            $validatedData = $request->validated();
            $user          = $service->loginUser($validatedData);

            if (empty($user)) {
                return 'Неверный логин или пароль';
            }

            return $user;
        } catch (\Exception) {
            throw new BaseException('Unknown error');
        }
    }

    public function logout()
    {
        try {
            $user = \Auth::user();
            $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        } catch (BaseException) {
            throw new BaseException('Unknown error');
        }
    }
}
