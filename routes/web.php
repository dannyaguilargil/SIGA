<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
    Route::get('/registro', [AuthController::class, 'showRegistration'])->name('register');
    Route::post('/registro', [AuthController::class, 'register'])->name('register.store');
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');
    Route::get('/recuperar-contrasena', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/recuperar-contrasena', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/restablecer-contrasena/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/restablecer-contrasena', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function (): void {
    Route::view('/inicio', 'dashboard')->name('dashboard');
    Route::post('/cerrar-sesion', [AuthController::class, 'destroy'])->name('logout');
});
