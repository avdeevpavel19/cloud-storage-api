<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidEmailUpdateTokenException;
use App\Exceptions\UserNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendEmailUpdateRequest;
use App\Http\Requests\Api\UpdateLoginUserRequest;
use App\Models\User;
use App\Services\Api\UserService;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;

class UserController extends Controller
{
    use HttpResponse;

    private UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function getInfo(): JsonResponse
    {
        try {
            $currentUser = \Auth::user();

            $result = $this->service->getInfo($currentUser);

            return $this->success($result);
        } catch (\Exception $e) {
            return $this->error('Unknown error');
        }
    }

    public function updateLogin(UpdateLoginUserRequest $request): JsonResponse
    {
        try {
            $validationData = $request->validated();
            $currentUser    = \Auth::user();

            $result = $this->service->updateLogin($validationData, $currentUser);

            if ($result) {
                return $this->message('Логин успешно обновлен');
            }
        } catch (UserNotFoundException $userNotFoundException) {
            return $this->error($userNotFoundException->getMessage());
        } catch (Exception $e) {
            return $this->error('Unknown error');
        }
    }

    public function sendEmailUpdate(SendEmailUpdateRequest $request): JsonResponse
    {
        try {
            $validationData = $request->validated();
            $result         = $this->service->sendEmailUpdate($validationData);

            if ($result) {
                return $this->message($result);
            }
        } catch (Exception $e) {
            return $this->error('Unknown error');
        }
    }

    public function updateEmail(): JsonResponse
    {
        try {
            $currentUser = Auth::user();
            $selectUser  = User::where('email', $currentUser->email)->first();
            $hashFromURL = \Request::segment(3);

            $result = $this->service->updateEmail($hashFromURL, $selectUser);

            if ($result) {
                return $this->message($result);
            }
        } catch (InvalidEmailUpdateTokenException $invalidEmailUpdateTokenException) {
            return $this->error($invalidEmailUpdateTokenException->getMessage());
        } catch (\Exception $e) {
            return $this->error('Unknown error');
        }
    }
}
