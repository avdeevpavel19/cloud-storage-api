<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\PasswordResetRequest;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mockery\Exception;

class PasswordResetController extends Controller
{
    use HttpResponse;

    public function sendLinkEmail(Request $request)
    {
        try {
            $request->validate(['email' => ['required', 'email']]);

            $email = $request->input('email');
            $token = \Str::random(64);

            $existingToken = \DB::table('password_reset_tokens')
                ->where('email', $email)
                ->first();

            if ($existingToken) {
                \DB::table('password_reset_tokens')
                    ->where('email', $email)
                    ->update(['token' => $token]);
            } else {
                \DB::table('password_reset_tokens')->insert([
                    'email'      => $email,
                    'token'      => $token,
                    'created_at' => now()
                ]);
            }

            $resetLink = url('/api/password/reset/' . $token);

            \Mail::to($email)->send(new ResetPasswordMail($resetLink));

            return response()->json(['message' => 'Вам отправлено письмо для сброса пароля']);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function reset(PasswordResetRequest $request)
    {
        try {
            $hashFromURL = $request->segment(4);

            $reset = DB::table('password_reset_tokens')
                ->where('token', $hashFromURL)
                ->first();

            if ($reset == NULL || now()->subHours(2) > $reset->created_at) {
                return response()->json(['message' => 'Недействительная ссылка для сброса пароля'], 422);
            }

            $user = User::where('email', $reset->email)->first();
            $user->update(['password' => Hash::make($request->input('password'))]);

            DB::table('password_reset_tokens')->where('email', $reset->email)->delete();

            return response()->json(['message' => 'Пароль успешно сброшен']);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
