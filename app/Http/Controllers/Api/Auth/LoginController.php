<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\BaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Services\Api\Auth\LoginService;
use App\Traits\HttpResponse;
use Exception;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    use HttpResponse;

    public function store(LoginRequest $request, LoginService $service)
    {
        try {
            $validatedData = $request->validated();
            $user          = $service->loginUser($validatedData);

            if (empty($user)) {
                return $this->info('Неверный логин или пароль');
            }

            return $user;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }

    public function logout(): string
    {
        try {
            $user = \Auth::user();
            $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

            return $this->info('Вы вышли из системы');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }
}
