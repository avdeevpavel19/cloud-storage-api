<?php

namespace App\Http\Controllers\Api;

use App\DTO\Api\UserDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendEmailUpdateRequest;
use App\Http\Requests\Api\UpdateLoginUserRequest;
use App\Mail\EmailUpdateMail;
use App\Models\User;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Mockery\Exception;

class UserController extends Controller
{
    use HttpResponse;

    /**
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function getInfo(): JsonResponse
    {
        try {
            $currentUser = \Auth::user();

            $userDTO = new UserDto(
                $currentUser->id,
                $currentUser->login,
                $currentUser->email,
                $currentUser->disk_space,
            );

            return $this->success($userDTO);
        } catch (\Exception $e) {
            return $this->error('Unknown error');
        }
    }

    public function updateLogin(UpdateLoginUserRequest $request)
    {
        try {
            $user = Auth::user();

            if (empty($user)) {
                return $this->notFound('Пользователь не найден');
            }

            $user->login = $request->login;
            $user->save();

            return $this->success('Логин успешно обновлен');
        } catch (Exception $e) {
            return $this->error('Unknown error');
        }
    }

    public function sendEmailUpdate(SendEmailUpdateRequest $request)
    {
        try {
            $validationData = $request->validated();

            $email = $validationData['new_email'];
            $token = \Str::random(64);

            $existingRecord = \DB::table('update_email_tokens')->where('email', $email)->first();

            if ($existingRecord) {
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

            return response()->json(['message' => 'Вам отправлено письмо для изменения старой почты на новую']);
        } catch (Exception $e) {
            return $this->error('Unknown error');
        }
    }

    public function updateEmail(Request $request)
    {
        try {
            $currentUser = Auth::user();
            $selectUser  = User::where('email', $currentUser->email)->first();
            $hashFromURL = $request->segment(3);

            $updatedEmailToken = \DB::table('update_email_tokens')->where('token', $hashFromURL)->first();

            if ($updatedEmailToken == NULL) {
                return $this->message('Не валидный токен.Попробуйте еще раз');
            }

            $selectUser->email = $updatedEmailToken->email;
            $selectUser->save();

            if ($selectUser) {
                return $this->success('Почта успешно обновлена');
            }
        } catch (\Exception $e) {
            return $this->error('Unknown error');
        }
    }
}
