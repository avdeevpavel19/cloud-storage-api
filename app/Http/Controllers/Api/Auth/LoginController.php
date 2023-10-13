<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Services\Api\Auth\LoginService;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use HttpResponse;

    /**
     * @param LoginRequest $request Запрос на вход, содержащий валидированные данные.
     * @param LoginService $service Сервис для выполнения входа пользователя.
     *
     * @return \Illuminate\Http\JsonResponse Ответ JSON с информацией о пользователе и токеном доступа в случае успешной аутентификации,
     * либо сообщение об ошибке в случае неудачи.
     */
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
