<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\BaseException;
use App\Exceptions\EmailUpdateException;
use App\Exceptions\InvalidEmailUpdateTokenException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendEmailUpdateRequest;
use App\Http\Requests\Api\UpdateLoginUserRequest;
use App\Services\Api\UserService;
use App\Traits\HttpResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use HttpResponse;

    private UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function getInfo(): array
    {
        try {
            $currentUser = \Auth::user();

            return [
                'id'                  => $currentUser->id,
                'login'               => $currentUser->login,
                'email'               => $currentUser->email,
                'occupied_disk_space' => $currentUser->occupied_disk_space,
            ];
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }

    public function updateLogin(UpdateLoginUserRequest $request): array
    {
        try {
            $validationData = $request->validated();
            $currentUser    = \Auth::user();

            $updatedLogin = $this->service->updateLogin($validationData['login'], $currentUser);

            return [
                'id'    => $updatedLogin->id,
                'login' => $updatedLogin->login
            ];
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }

    public function sendEmailUpdate(SendEmailUpdateRequest $request): JsonResponse
    {
        try {
            $validationData = $request->validated();
            $this->service->sendEmailUpdate($validationData['new_email']);

            return $this->info('Вам на почту отправлено письмо для сброса почты');
        } catch (EmailUpdateException) {
            throw new EmailUpdateException('Невозможно обновить электронную почту пользователя');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }

    public function updateEmail(): JsonResponse
    {
        try {
            $hashFromURL = \Request::segment(4);

            $this->service->confirmNewEmailByToken($hashFromURL);

            return $this->info('Почта успешно изменена');
        } catch (InvalidEmailUpdateTokenException) {
            return $this->error('Не валидный токен для обновления почты');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }
}
