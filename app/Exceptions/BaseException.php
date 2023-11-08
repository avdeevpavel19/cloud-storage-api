<?php

namespace App\Exceptions;

use Exception;

class BaseException extends Exception
{
    public function render()
    {
        return response()->json([
            'message'   => $this->getMessage(),
            'exception' => get_class($this),
        ], 500);
    }
}
