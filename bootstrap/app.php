<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/website.php'));
        },
    )
   ->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\TrackPageVisit::class,
    ]);

    $middleware->alias([
        'has.business' => \App\Http\Middleware\HasBusiness::class,
        'permission'   => \App\Http\Middleware\CheckPermission::class,
        'admin'        => \App\Http\Middleware\AdminMiddleware::class,
    ]);
    
    // Exclude M-Pesa and Paystack webhooks from CSRF verification
    $middleware->validateCsrfTokens(except: [
        'subscription/mpesa/callback',
        'subscription/paystack/webhook',
    ]);
})
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
