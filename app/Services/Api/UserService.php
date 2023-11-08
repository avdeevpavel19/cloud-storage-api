<?php

namespace App\Services\Api;

use App\Exceptions\EmailUpdateException;
use App\Exceptions\InvalidEmailUpdateTokenException;
use App\Mail\EmailUpdateMail;
use App\Models\User;

class UserService
{
    public function updateLogin(string $login, User $user): User
    {
        $user->login = $login;
        $user->saveOrFail();

        return $user;
    }

    /**
     * @throws EmailUpdateException
     */
    public function sendEmailUpdate(string $email): void
    {
        $token         = \Str::random(64);
        $currentUserID = \Auth::id();

        $existingEmailToken = \DB::table('update_email_tokens')->where('email', $email)->first();

        if ($existingEmailToken) {
            \DB::table('update_email_tokens')->where('email', $email)->update([
                'email'      => $email,
                'user_id'    => $currentUserID,
                'token'      => $token,
                'created_at' => now()
            ]);
        } else {
            \DB::table('update_email_tokens')->insert([
                'email'      => $email,
                'user_id'    => $currentUserID,
                'token'      => $token,
                'created_at' => now()
            ]);
        }

        if ($existingEmailToken) {
            $updateLink = url('/api/user/email/' . $token);
            \Mail::to($email)->send(new EmailUpdateMail($updateLink));

            return;
        }

        throw new EmailUpdateException;
    }

    /**
     * @throws InvalidEmailUpdateTokenException
     */
    public function confirmNewEmailByToken(string $emailUpdateToken): void
    {
        $updatedEmailTokenRecord = \DB::table('update_email_tokens')->where('token', $emailUpdateToken)->first();

        $user = User::find($updatedEmailTokenRecord->user_id);

        if ($updatedEmailTokenRecord === NULL) {
            throw new InvalidEmailUpdateTokenException;
        }

        $user->email = $updatedEmailTokenRecord->email;
        $user->saveOrFail();
    }
}
