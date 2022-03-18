<?php

namespace Arispati\LaravelLogS3\Providers;

use Arispati\LaravelLogS3\Manager\Log;
use Illuminate\Support\ServiceProvider;

class LaravelLogS3Provider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
              __DIR__ . '/../Config/logs3.php' => config_path('logs3.php'),
            ], 'config');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/logs3.php', 'logs3');

        $this->app->singleton(Log::class, function () {
            return new Log();
        });
    }
}
