<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Mockery\Exception;

class VerificationController extends Controller
{
    public function sendVerificationNotification()
    {
        try {
            $user = \Auth::user();

            if ($user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Ваша почта уже подтверждена']);
            }

            $user->sendEmailVerificationNotification();

            return response()->json(['message' => 'Вам отправлено письмо для подтверждения']);
        } catch (Exception $e) {
            return response()->json('Unknown error');
        }
    }

    public function verify()
    {
        try {
            $user = User::find(\Auth::id());

            if ($user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Пользователь уже прошел верификацию']);
            }

            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }

            return response()->json(['message' => 'Верификация успешно пройдена']);
        } catch (Exception $e) {
            return response()->json('Unknown error');
        }
    }
}
