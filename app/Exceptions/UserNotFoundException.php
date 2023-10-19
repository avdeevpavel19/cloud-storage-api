<?php

namespace App\Exceptions;

use Exception;

class UserNotFoundException extends Exception
{
    public function __construct($message = 'Пользователь не найден', $code = 404, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render()
    {
        return response(['error' => $this->getMessage()], $this->getCode());
    }
}
