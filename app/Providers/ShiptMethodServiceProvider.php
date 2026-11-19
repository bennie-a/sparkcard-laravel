<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ShiptMethodServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('ShiptMethodServ', 'App\Services\Shipt\ShiptMethodService');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
