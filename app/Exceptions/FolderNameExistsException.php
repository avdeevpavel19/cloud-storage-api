<?php

namespace App\Exceptions;

use Exception;

class FolderNameExistsException extends Exception
{
    public function __construct($message = 'У вас уже есть папка с таким названием', $code = 400, Exception $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render()
    {
        return response(['error' => $this->getMessage()], $this->getCode());
    }
}
