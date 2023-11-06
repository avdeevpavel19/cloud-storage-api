<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\BaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\CreateUserRequest;
use App\Services\Api\Auth\RegisterService;
use App\Traits\HttpResponse;

class RegisterController extends Controller
{
    use HttpResponse;

    public function store(CreateUserRequest $request, RegisterService $service): array
    {
        try {
            $validatedData = $request->validated();
            $createdUser   = $service->createUser($validatedData);

            return $createdUser;
        } catch (\Exception) {
            throw new BaseException('Unknown error');
        }
    }
}
