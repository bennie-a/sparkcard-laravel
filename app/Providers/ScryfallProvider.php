<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ScryfallProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('ScryfallServ', 'App\Services\Api\ScryfallService');
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
