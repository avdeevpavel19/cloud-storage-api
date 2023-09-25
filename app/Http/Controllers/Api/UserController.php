<?php

namespace App\Http\Controllers\Api;

use App\DTO\Api\UserDto;
use App\Http\Controllers\Controller;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;

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
}
