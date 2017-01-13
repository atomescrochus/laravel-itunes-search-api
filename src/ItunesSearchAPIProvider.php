<?php

namespace Atomescrochus\ItunesStore;

use Illuminate\Support\ServiceProvider;

class ItunesSearchAPIProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/laravel-itunes-search-api.php' => config_path('laravel-itunes-search-api.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/config/laravel-itunes-search-api.php', 'laravel-itunes-search-api');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
