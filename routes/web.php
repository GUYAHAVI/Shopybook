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
// routes/web.php
Route::middleware(['auth', 'verified'])->group(function () {
    // Business Profile Routes
    Route::prefix('business')->group(function () {
        Route::get('/create', [BusinessController::class, 'create'])->name('business.create');
        Route::post('/store', [BusinessController::class, 'store'])->name('business.store');
        Route::get('/edit', [BusinessController::class, 'edit'])->name('business.edit');
        Route::put('/update', [BusinessController::class, 'update'])->name('business.update');
        Route::delete('/delete', [BusinessController::class, 'destroy'])->name('business.destroy');
    });
});