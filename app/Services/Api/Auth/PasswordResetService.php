<?php

namespace App\Services\Api\Auth;

use App\Exceptions\InvalidResetPasswordLinkException;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PasswordResetService
{
    public function sendLinkEmail(array $data): void
    {
        $email      = $data['email'];
        $resetToken = \Str::random(64);

        $existingToken = \DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if ($existingToken) {
            \DB::table('password_reset_tokens')
                ->where('email', $email)
                ->update(['token' => $resetToken]);
        } else {
            \DB::table('password_reset_tokens')->insert([
                'email'      => $email,
                'token'      => $resetToken,
                'created_at' => now()
            ]);
        }

        $resetLink = url('/api/password/' . $resetToken);

        \Mail::to($data['email'])->send(new ResetPasswordMail($resetLink));
    }

    /**
     * @throws InvalidResetPasswordLinkException
     */
    public function reset(array $data): void
    {
        $resetTokenFromURL = \Request::segment(3);

        $resetTokenRecord = DB::table('password_reset_tokens')
            ->where('token', $resetTokenFromURL)
            ->first();

        if ($resetTokenRecord == NULL || now()->subHours(2) > $resetTokenRecord->created_at) {
            throw new InvalidResetPasswordLinkException;
        }

        $userWithEmail = User::where('email', $resetTokenRecord->email)->first();
        $userWithEmail->update(['password' => Hash::make($data['password'])]);

        DB::table('password_reset_tokens')->where('email', $resetTokenRecord->email)->delete();
    }
}
