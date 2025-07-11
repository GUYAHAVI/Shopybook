<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Business;
use Illuminate\Support\Facades\Route;
use App\Services\LocalizationService;

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

        if (!function_exists('t')) {
            function t($key, $context = 'ui', $parameters = []) {
                $service = app(LocalizationService::class);
                return $service->translate($key, $context, $parameters);
            }
        }
    }
}
