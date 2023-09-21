<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\CreateUserRequest;
use App\Services\Api\Auth\RegisterService;
use App\Traits\HttpResponse;

class RegisterController extends Controller
{
    use HttpResponse;

    public function store(CreateUserRequest $request, RegisterService $service)
    {
        try {
            $validatedData = $request->validated();
            $user          = $service->createUser($validatedData);

            return $this->success($user);
        } catch (\Exception $e) {
            return $this->error('Unknown error');
        }
    }
}
