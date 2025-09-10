<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ExhibitorAnalyticsProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // we have to get the exhibitor and delegate count from the exhibitionParticapation table

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
