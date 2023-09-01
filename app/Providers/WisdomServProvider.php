<?php

namespace App\Providers;

use App\Services\WisdomGuildService;
use Illuminate\Support\ServiceProvider;

class WisdomServProvider extends ServiceProvider
{
   /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('WisdomGuild', 'App\Services\WisdomGuildService');
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
