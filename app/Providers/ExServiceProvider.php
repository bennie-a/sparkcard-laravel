<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ExServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('ExService', 'App\Services\ExpansionService');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
