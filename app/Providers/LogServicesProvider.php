<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Log\LogServiceProvider as SysServiceProvider;
use Illuminate\Log\Writer;

class LogServicesProvider extends SysServiceProvider
{
    protected $log_file;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->load_request_id();
        $this->log_configure();
    }

    protected function configureSingleHandler(Writer $log)
    {
        $log->useFiles('/var/logs/app/laravel.log', $this->logLevel());
    }

    protected function configureDailyHandler(Writer $log)
    {
        $log->useDailyFiles('/var/logs/app/laravel.log', $this->maxFiles(), $this->logLevel());
    }

    protected function load_request_id()
    {
        define( 'REQUEST_ID' , config('app.log_prefix').Carbon::now()->timestamp );
    }

    /**
    * 注册 monolog pushHandler
    * @return void
    */
    protected function log_configure()
    {
        $log_file = $this->getLogFile();
        $log_max_files = $this->getLogMaxFiles();
    }
}