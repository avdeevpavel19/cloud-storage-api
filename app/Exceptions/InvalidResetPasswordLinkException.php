<?php

namespace App\Exceptions;

class InvalidResetPasswordLinkException extends \Exception
{
    protected $message = 'Недействительная ссылка для сброса пароля.';
}
