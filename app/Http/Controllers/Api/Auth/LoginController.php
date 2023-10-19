<?php

namespace App\Http\Controllers\Api\Auth;

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

            if (!empty($user)) {
                return $this->success($user);
            }

            return $this->notFound('Неверный логин или пароль');
        } catch (\Exception $e) {
            return $this->error('Unknown error');
        }
    }

    public function logout()
    {
        try {
            $user = \Auth::user();
            $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

            return response()->json(['message' => 'Вы вышли из аккаунта']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unknown error']);
        }
    }
}
