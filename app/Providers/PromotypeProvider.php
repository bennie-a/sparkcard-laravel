<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PromotypeProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Promo', 'App\Services\PromoService');
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
