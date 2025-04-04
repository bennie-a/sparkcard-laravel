<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class APIHandlerProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('APIHand', 'App\Http\Controllers\Handler\APIHandler');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
