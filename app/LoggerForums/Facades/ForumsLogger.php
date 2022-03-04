<?php

namespace App\LoggerForums\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Illuminate\Log\Writer
 */
class ForumsLogger extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'forumslogger';
    }
}
