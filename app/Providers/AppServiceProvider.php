<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Business;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    // Bootstrap any application services.


    public function boot()
    {
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $view->with('business', auth()->user()->business);
            }
        });
    }
}
