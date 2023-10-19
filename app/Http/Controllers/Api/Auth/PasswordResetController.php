<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\InvalidResetLinkException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\PasswordResetRequest;
use App\Http\Requests\Api\Auth\PasswordResetSendLinkEmailRequest;
use App\Services\Api\Auth\PasswordResetService;
use App\Traits\HttpResponse;
use Mockery\Exception;

class PasswordResetController extends Controller
{
    use HttpResponse;

    private PasswordResetService $service;

    public function __construct(PasswordResetService $service)
    {
        $this->service = $service;
    }

    public function sendLinkEmail(PasswordResetSendLinkEmailRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $result        = $this->service->sendLinkEmail($validatedData);

            return $this->message($result);
        } catch (Exception $e) {
            return $this->error('Unknown error');
        }
    }

    public function reset(PasswordResetRequest $request)
    {
        try {
            $validationData = $request->validated();
            $result         = $this->service->reset($validationData);

            return $this->message($result);
        } catch (InvalidResetLinkException $invalidResetLinkException) {
            return $this->error($invalidResetLinkException->getMessage());
        } catch (\Exception $e) {
            return $this->error('Unknown error');
        }
    }
}
