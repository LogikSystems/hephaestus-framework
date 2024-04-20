<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->commands([
            \App\Commands\Boot::class,
        ]);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
