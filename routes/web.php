<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TenantRegistrationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register-business', [TenantRegistrationController::class, 'showForm'])->name('register.business');
Route::post('/register-business', [TenantRegistrationController::class, 'register']);


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
