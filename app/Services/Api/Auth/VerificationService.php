<?php

namespace App\Services\Api\Auth;

use App\Exceptions\EmailAlreadyVerifiedException;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

class VerificationService
{
    /**
     * @throws EmailAlreadyVerifiedException
     */
    public function sendVerificationNotification(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            throw new EmailAlreadyVerifiedException();
        }

        $user->sendEmailVerificationNotification();
    }

    public function verify(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
    }
}
