<?php

use App\Http\Controllers\PublicWebsiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Website Routes (Environment-Aware)
|--------------------------------------------------------------------------
|
| These routes handle public-facing websites with environment detection:
| - Production: {businessslug}.shopybook.com (subdomain)
| - Local: localhost/website/{businessslug} (path-based)
|
*/

// Check environment for routing strategy
$appUrl = config('app.url', '');
$useSubdomainLocally = str_contains($appUrl, '.localhost');

if ($useSubdomainLocally) {
    // LOCAL DEVELOPMENT with .localhost: Subdomain routing
    Route::domain('{subdomain}.localhost')->name('public.website.')->group(function () {
        Route::get('/', [PublicWebsiteController::class, 'homepage'])->name('home');
        Route::post('/contact', [PublicWebsiteController::class, 'submitContact'])->name('contact');
        Route::get('/api/products/{productId}', [PublicWebsiteController::class, 'getProduct'])->name('product');
        Route::get('/{slug}', [PublicWebsiteController::class, 'page'])->name('page');
    });
} elseif (config('app.env') === 'local' || str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
    // LOCAL DEVELOPMENT: Path-based routing
    Route::prefix('website/{subdomain}')->name('public.website.')->group(function () {
        Route::get('/', [PublicWebsiteController::class, 'homepage'])->name('home');
        Route::post('/contact', [PublicWebsiteController::class, 'submitContact'])->name('contact');
        Route::get('/api/products/{productId}', [PublicWebsiteController::class, 'getProduct'])->name('product');
        Route::get('/{slug}', [PublicWebsiteController::class, 'page'])->name('page');
    });
} else {
    // PRODUCTION: Subdomain routing
    Route::domain('{subdomain}.shopybook.com')->name('public.website.')->group(function () {
        Route::get('/', [PublicWebsiteController::class, 'homepage'])->name('home');
        Route::post('/contact', [PublicWebsiteController::class, 'submitContact'])->name('contact');
        Route::get('/api/products/{productId}', [PublicWebsiteController::class, 'getProduct'])->name('product');
        Route::get('/{slug}', [PublicWebsiteController::class, 'page'])->name('page');
    });
}

