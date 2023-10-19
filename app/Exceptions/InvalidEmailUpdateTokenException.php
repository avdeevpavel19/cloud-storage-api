<?php

namespace App\Exceptions;

use Exception;

class InvalidEmailUpdateTokenException extends Exception
{
    public function __construct($message = 'Не валидный токен для обновления почты', $code = 400, Exception $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render()
    {
        return response(['error' => $this->getMessage()], $this->getCode());
    }
}
