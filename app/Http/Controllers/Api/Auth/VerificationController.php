<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\BaseException;
use App\Exceptions\EmailAlreadyVerifiedException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Api\Auth\VerificationService;

class VerificationController extends Controller
{
    private VerificationService $service;

    public function __construct(VerificationService $service)
    {
        $this->service = $service;
    }

    public function sendVerificationNotification()
    {
        try {
            $user = \Auth::user();
            $this->service->sendVerificationNotification($user);
        } catch (EmailAlreadyVerifiedException) {
            throw new EmailAlreadyVerifiedException('Почта уже верифицирована');
        } catch (BaseException) {
            throw new BaseException('Unknown error');
        }
    }

    public function verify()
    {
        try {
            $user = User::find(\Auth::id());
            $this->service->verify($user);
        } catch (BaseException) {
            throw new BaseException('Unknown error');
        }
    }
}
