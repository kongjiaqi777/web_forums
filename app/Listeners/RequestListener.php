<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RequestListener
{
    const REQUEST_LIMIT_SIZE = 8388608; //8 * 1024 * 1024 8M by default, Depends on php.ini post_max_size

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $paramStr = json_encode($event->all()) ?: '';
        $size = mb_strlen($paramStr, '8bit');

        $paramStrForLog = self::REQUEST_LIMIT_SIZE >= $size
            ? $paramStr
            : substr($paramStr, 0, self::REQUEST_LIMIT_SIZE) . '...}';
       
        $log = '[' . date('Y-m-d H:i:s') . '] ' . $paramStrForLog . "\r\n";
        $fileName = 'request-' . date('Y-m-d') . '.log';
        $filepath = storage_path('logs/request/'.$fileName);
        $fc = fopen($filepath, 'a');
        fwrite($fc, $log);
        fclose($fc);
    }
}
