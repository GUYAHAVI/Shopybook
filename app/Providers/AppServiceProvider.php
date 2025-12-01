<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

use App\Models\Business;
use App\Models\OrderReturn;
use App\Models\Supplier;
use App\Policies\OrderReturnPolicy;
use App\Policies\SupplierPolicy;
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
        // Force URL scheme and root URL from config
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
        }
        
        // Register policies
        Gate::policy(OrderReturn::class, OrderReturnPolicy::class);
        Gate::policy(Supplier::class, SupplierPolicy::class);
        
        // Register explicit route model binding for 'return' parameter
        Route::bind('return', function ($value) {
            return OrderReturn::findOrFail($value);
        });
        
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $view->with('userBusiness', auth()->user()->business);
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
