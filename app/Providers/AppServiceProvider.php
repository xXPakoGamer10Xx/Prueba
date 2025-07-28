<?php

namespace App\Providers;

// CAMBIO APLICADO AQUÍ
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // CAMBIO APLICADO AQUÍ
        Paginator::useBootstrapFive();
    }
}