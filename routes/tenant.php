<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

use Illuminate\Support\Facades\Auth;

Auth::routes();

Route::get('/dashboard', function () {
    return view('dashboard'); // You can customize this
})->middleware('auth');


Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/', function () {
        return view('tenant.dashboard');
    })->name('tenant.dashboard');

    // Tenant-specific routes will go here
});
