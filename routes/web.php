<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TenantRegistrationController;
use App\Http\Controllers\BusinessController;

Route::get('/', function () {
    return view('index');
});




Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'dashboard'])->name('dashboard')->middleware('auth');




Route::middleware(['auth', 'verified'])->group(function () {
    // Business Profile Routes
    Route::prefix('business')->group(function () {
        Route::get('/create', [BusinessController::class, 'create'])->name('business.create');
        Route::post('/store', [BusinessController::class, 'store'])->name('business.store');
        Route::get('/edit', [BusinessController::class, 'edit'])->name('business.edit')
            ->middleware('has.business');
        Route::put('/update', [BusinessController::class, 'update'])->name('business.update')
            ->middleware('has.business');
        Route::delete('/delete', [BusinessController::class, 'destroy'])->name('business.destroy')
            ->middleware('has.business');
    });

    // Tenant routes (protected by business check)
    Route::middleware('has.business')->group(function () {
        Route::prefix('dashboard')->group(function () {
            Route::get('/', function () {
                return view('dashboard');
            })->name('dashboard');
            
            // Other protected routes...
        });
    });
});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
