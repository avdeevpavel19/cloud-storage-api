<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Api\Auth\VerificationService;
use Mockery\Exception;

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
            $user   = \Auth::user();
            $result = $this->service->sendVerificationNotification($user);

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json('Unknown error');
        }
    }

    public function verify()
    {
        try {
            $user   = User::find(\Auth::id());
            $result = $this->service->verify($user);

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json('Unknown error');
        }
    }
}
