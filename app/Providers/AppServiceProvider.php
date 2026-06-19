<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
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
        $this->configureAssetUrlForProjectRoot();

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

    /**
     * When cPanel uses the project root as the web root (root index.php + .htaccess),
     * asset() must point at /public/css, /public/img, etc.
     */
    protected function configureAssetUrlForProjectRoot(): void
    {
        if (env('ASSET_URL') || $this->app->runningInConsole()) {
            return;
        }

        $script = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');

        if (! str_ends_with($script, '/index.php') || str_ends_with($script, '/public/index.php')) {
            return;
        }

        $assetRoot = rtrim((string) config('app.url'), '/').'/public';
        Config::set('app.asset_url', $assetRoot);
        Config::set('filesystems.disks.public.url', $assetRoot.'/storage');
    }
}
