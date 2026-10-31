<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
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
        \App\Http\Middleware\Honeypot::class,
    ]);

    $middleware->alias([
        'has.business' => \App\Http\Middleware\HasBusiness::class,
        'permission'   => \App\Http\Middleware\CheckPermission::class,
        'owner.only'   => \App\Http\Middleware\EnsureBusinessOwner::class,
        'admin'        => \App\Http\Middleware\AdminMiddleware::class,
        'honeypot'     => \App\Http\Middleware\Honeypot::class,
    ]);
    
    // Trust proxy headers (ngrok, load balancers) so generated URLs are https
    $middleware->trustProxies(at: '*');

    // Exclude M-Pesa and Paystack webhooks from CSRF verification
    $middleware->validateCsrfTokens(except: [
        'subscription/mpesa/callback',
        'subscription/paystack/webhook',
        'mpesa/stk-callback',
    ]);
})
    ->withExceptions(function (Exceptions $exceptions) {
        // An expired CSRF token (419 "Page Expired") sends the user back to the
        // form they were on with a friendly message instead of a dead-end page.
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your session expired — reload the page and try again.',
                ], 419);
            }

            return redirect()->back()
                ->withInput($request->except('_token', 'password', 'password_confirmation'))
                ->with('error', 'Your session expired — please try again.');
        });
    })->create();
