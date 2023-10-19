<?php

namespace App\Services\Api;

use App\DTO\Api\UserDto;
use App\Exceptions\InvalidEmailUpdateTokenException;
use App\Exceptions\UserNotFoundException;
use App\Mail\EmailUpdateMail;
use App\Models\User;

class UserService
{
    public function getInfo(User $user): UserDto
    {
        $userDTO = new UserDto(
            $user->id,
            $user->login,
            $user->email,
            $user->occupied_disk_space,
        );

        return $userDTO;
    }

    public function updateLogin(array $data, User $user): User
    {
        if (empty($user)) {
            throw new UserNotFoundException;
        }

        $user->login = $data['login'];
        $user->save();

        return $user;
    }

    public function sendEmailUpdate(array $data): string
    {
        $email = $data['new_email'];
        $token = \Str::random(64);

        $existingEmailToken = \DB::table('update_email_tokens')->where('email', $email)->first();

        if ($existingEmailToken) {
            \DB::table('update_email_tokens')->where('email', $email)->update([
                'email'      => $email,
                'token'      => $token,
                'created_at' => now()
            ]);
        } else {
            \DB::table('update_email_tokens')->insert([
                'email'      => $email,
                'token'      => $token,
                'created_at' => now()
            ]);
        }

        $updateLink = url('/api/update-email/' . $token);

        \Mail::to($email)->send(new EmailUpdateMail($updateLink));

        return 'Вам отправлено письмо для изменения старой почты на новую';
    }

    public function updateEmail(string $emailUpdateToken, User $user): string
    {
        $updatedEmailTokenRecord = \DB::table('update_email_tokens')->where('token', $emailUpdateToken)->first();

        if ($updatedEmailTokenRecord == NULL) {
            throw new InvalidEmailUpdateTokenException;
        }

        $user->email = $updatedEmailTokenRecord->email;
        $user->save();

        if ($user) {
            return 'Почта успешно обновлена';
        }
    }
}
