<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MtgDevProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('MtgDev', 'App\Services\MtgDevService');
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
