<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\BaseException;
use App\Exceptions\EmailAlreadyVerifiedException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Api\Auth\VerificationService;
use App\Traits\HttpResponse;
use Exception;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    use HttpResponse;

    private VerificationService $service;

    public function __construct(VerificationService $service)
    {
        $this->service = $service;
    }

    public function sendVerificationNotification(): string
    {
        try {
            $currentUser = \Auth::user();
            $this->service->sendVerificationNotification($currentUser);

            return $this->info('Вам на почту отправлено письмо для верификацрии');
        } catch (EmailAlreadyVerifiedException) {
            throw new EmailAlreadyVerifiedException('Почта уже верифицирована');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }

    public function verify(): string
    {
        try {
            $user = User::find(\Auth::id());
            $this->service->verify($user);

            return $this->info('Ваша почта успешно верифицирована');
        } catch (EmailAlreadyVerifiedException) {
            throw new EmailAlreadyVerifiedException('Почта уже верифицирована');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }
}
