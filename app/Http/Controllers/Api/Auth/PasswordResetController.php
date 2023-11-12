<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\BaseException;
use App\Exceptions\InvalidResetPasswordLinkException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\PasswordResetRequest;
use App\Http\Requests\Api\Auth\PasswordResetSendLinkEmailRequest;
use App\Services\Api\Auth\PasswordResetService;
use App\Traits\HttpResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    use HttpResponse;

    private PasswordResetService $service;

    public function __construct(PasswordResetService $service)
    {
        $this->service = $service;
    }

    public function sendLinkEmail(PasswordResetSendLinkEmailRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $this->service->sendLinkEmail($validatedData);

            return $this->info('Вам на почту отправлено письмо для сброса пароля');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }

    public function reset(PasswordResetRequest $request): JsonResponse
    {
        try {
            $validationData = $request->validated();
            $this->service->reset($validationData);

            return $this->info('Пароль успешно сброшен');
        } catch (InvalidResetPasswordLinkException) {
            throw new InvalidResetPasswordLinkException('Недействительная ссылка для сброса пароля');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }
}
