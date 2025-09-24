<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Fix mbstring encoding for phiki/phiki compatibility
        if (function_exists('mb_regex_encoding')) {
            mb_regex_encoding('UTF-8');
        }

        if (function_exists('mb_internal_encoding')) {
            mb_internal_encoding('UTF-8');
        }

        // Register model observers
        $this->registerObservers();
    }

    /**
     * Register model observers
     */
    private function registerObservers(): void
    {
        // Trello integration observers
        \App\Models\Service::observe(\App\Observers\ServiceTrelloObserver::class);
    }
}
