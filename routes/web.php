<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TenantRegistrationController;
use App\Http\Controllers\BusinessController;







Route::get('/',[IndexController::class,'index'])->name('index');
Route::get('/business', [BusinessController::class, 'index'])->name('businesses');

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'dashboard'])->name('dashboard')->middleware('auth');
// In routes/web.php
Route::get('business/{business:slug}', [BusinessController::class, 'show'])->name('businesses.show');



Route::middleware(['auth', 'verified'])->group(function () {
    // Business Profile Routes
    Route::prefix('business')->group(function () {
        Route::get('/create', [BusinessController::class, 'create'])->name('business.create');
        Route::post('/store', [BusinessController::class, 'store'])->name('business.store');
        Route::get('/edit', [BusinessController::class, 'edit'])->name('business.edit')
            ->middleware('has.business');
        Route::put('/update', [BusinessController::class, 'update'])->name('business.update')
            ->middleware('has.business');

        Route::delete('/business/{business}', [BusinessController::class, 'destroy'])
            ->name('business.destroy')
            ->middleware(['auth', 'verified']);
        Route::post('/password/verify', function (Request $request) {
            try {
                $valid = Hash::check($request->password, auth()->user()->password);

                return response()->json([
                    'valid' => $valid,
                    'message' => $valid ? 'Password verified' : 'Invalid password'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Verification failed'
                ], 500);
            }
        })->name('password.verify')->middleware('auth');
    });

    // Tenant routes (protected by business check)
    // routes/web.php
    Route::prefix('business')->group(function () {
        Route::post('/password/verify', [\App\Http\Controllers\Auth\PasswordVerificationController::class, 'verify'])
            ->name('password.verify')
            ->middleware(['auth', 'throttle:3,1']);
    });
});

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


