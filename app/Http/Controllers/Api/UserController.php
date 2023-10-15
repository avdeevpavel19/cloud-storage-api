<?php

namespace App\Http\Controllers\Api;

use App\DTO\Api\UserDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateLoginUserRequest;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;

class UserController extends Controller
{
    use HttpResponse;

    /**
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function getInfo(): JsonResponse
    {
        try {
            $currentUser = \Auth::user();

            $userDTO = new UserDto(
                $currentUser->id,
                $currentUser->login,
                $currentUser->email,
                $currentUser->disk_space,
            );

            return $this->success($userDTO);
        } catch (\Exception $e) {
            return $this->error('Unknown error');
        }
    }

    public function updateLogin(UpdateLoginUserRequest $request)
    {
        try {
            $user = Auth::user();

            if (empty($user)) {
                return $this->notFound('Пользователь не найден');
            }

            $user->login = $request->login;
            $user->save();

            return $this->success('Логин успешно обновлен');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
