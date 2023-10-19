<?php

namespace App\Services\Api\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;

class VerificationService
{
    public function sendVerificationNotification(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            return ['message' => 'Ваша почта уже подтверждена'];
        }

        $user->sendEmailVerificationNotification();

        return ['message' => 'Вам отправлено письмо для подтверждения'];
    }

    public function verify(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            return ['message' => 'Пользователь уже прошел верификацию'];
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return ['message' => 'Верификация успешно пройдена'];
    }
}
