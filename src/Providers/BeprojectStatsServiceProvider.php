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

        // Optional: Publish configuration
        $this->publishes([
            __DIR__.'/../config/beproject-stats.php' => config_path('beproject-stats.php'),
        ], 'beproject-stats-config');
    }
}