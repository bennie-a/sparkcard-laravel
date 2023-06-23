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
        $this->app->bind('Scryfall', 'App\Repositories\Api\Mtg\ScryfallRepository');
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
