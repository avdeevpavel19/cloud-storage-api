<?php

namespace App\Exceptions;

class InvalidResetLinkException extends \Exception
{
    protected $message = 'Недействительная ссылка для сброса пароля.';
}
