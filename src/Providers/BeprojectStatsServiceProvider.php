<?php

namespace Beproject\Monitor\Providers;

use Illuminate\Support\ServiceProvider;

class BeprojectStatsServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register any bindings or services
    }

    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../Routes/routes.php');
    }
}