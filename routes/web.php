<?php

use App\Http\Controllers\AplicativoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CredentialRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RequestFormController;
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
    Route::get('/inicio', DashboardController::class)->middleware('organization.admin')->name('dashboard');
    Route::view('/mi-cuenta', 'member')->name('member.home');
    Route::get('/solicitudes', [CredentialRequestController::class, 'index'])->name('requests.index');
    Route::get('/solicitudes/{form}', [CredentialRequestController::class, 'create'])->name('requests.create');
    Route::post('/solicitudes/{form}', [CredentialRequestController::class, 'store'])->name('requests.store');
    Route::post('/cerrar-sesion', [AuthController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth', 'organization.admin'])->prefix('administracion/formularios')->name('forms.')->group(function (): void {
    Route::get('/', [RequestFormController::class, 'index'])->name('index');
    Route::get('/crear', [RequestFormController::class, 'create'])->name('create');
    Route::post('/', [RequestFormController::class, 'store'])->name('store');
    Route::get('/{form}/editar', [RequestFormController::class, 'edit'])->name('edit');
    Route::put('/{form}', [RequestFormController::class, 'update'])->name('update');
    Route::delete('/{form}', [RequestFormController::class, 'destroy'])->name('destroy');
});

Route::middleware(['auth', 'organization.admin'])->prefix('administracion/aplicativos')->name('applications.')->group(function (): void {
    Route::get('/', [AplicativoController::class, 'index'])->name('index');
    Route::post('/', [AplicativoController::class, 'store'])->name('store');
    Route::delete('/{aplicativo}', [AplicativoController::class, 'destroy'])->name('destroy');
    Route::post('/{aplicativo}/elementos', [AplicativoController::class, 'storeModulo'])->name('elements.store');
    Route::delete('/{aplicativo}/elementos/{modulo}', [AplicativoController::class, 'destroyModulo'])->name('elements.destroy');
});
