<?php

namespace App\Exceptions;

use Exception;

class NoStackException extends Exception
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        $this->message = $message;
        $this->code = ($code == 0 ? -1 : $code);
    }
}
