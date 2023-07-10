<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CardBoardProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('CardBoard', 'App\Services\CardBoardService');
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
