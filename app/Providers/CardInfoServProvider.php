<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CardInfoServProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('CardInfoServ', 'App\Services\CardInfoDBService');
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
