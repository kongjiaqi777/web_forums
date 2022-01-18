<?php

namespace App\Exceptions;

use Exception;

class BaseException extends Exception
{
    private $logLevel;

    public function __construct($message = "", $code = 0, Exception $previous = null, $logLevel = 'error')
    {
        $this->message = $message;
        $this->code = ($code == 0 ? -1 : $code); //TODO, use code in config file
        $this->logLevel = $logLevel;
    }

    public function getLogLevel()
    {
        return $this->logLevel;
    }
}
