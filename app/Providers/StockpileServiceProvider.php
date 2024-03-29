<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class StockpileServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('StockpileServ', ' App\Services\Stock\StockpileService');
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
