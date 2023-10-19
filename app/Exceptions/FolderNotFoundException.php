<?php

namespace App\Exceptions;

use Exception;

class FolderNotFoundException extends \Exception
{
    public function __construct($message = 'Указанная папка не найдена', $code = 404, Exception $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render()
    {
        return response(['error' => $this->getMessage()], $this->getCode());
    }
}
