<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\LoggerForums\Logger;

class LogServiceProvider extends ServiceProvider
{
    // protected $defer = false;

    public function register()
    {
        $this->app->singleton('forumslogger', function ($app) {
            return new Logger;
        });
    }

    public function boot()
    {
        $this->app->make('forumslogger');
        $this->app->singleton('Psr\Log\LoggerInterface', function ($app) {
            return $this->app->make('forumslogger');
        });
    }
}