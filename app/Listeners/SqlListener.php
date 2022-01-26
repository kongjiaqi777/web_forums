<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SqlListener
{
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
        $sql = str_replace("?", "'%s'", $event->sql);
        $log = vsprintf($sql, $event->bindings);
        $log = '[' . date('Y-m-d H:i:s') . '] ' . $log . "\r\n";
        $fileName = 'sql-' . date('Y-m-d') . '.log';
        $filepath = storage_path('logs/sql/'.$fileName);
        $fc = fopen($filepath, 'a');
        fwrite($fc, $log);
        fclose($fc);
    }
}
