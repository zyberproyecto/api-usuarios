<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\SolicitudController;

// Preflight CORS (responde 204 a cualquier OPTIONS /api/*)
Route::options('{any}', fn () => response()->noContent())
    ->where('any', '.*');

// Healthcheck simple
Route::get('/health', fn () => ['ok' => true]);

// Registro desde la Landing → crea una SOLICITUD en estado 'pendiente'
Route::post('/register', [SolicitudController::class, 'store'])->name('register');

// Login único de socios (por CI sin puntos ni guiones, o por email)
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Rutas protegidas con token Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',      [AuthController::class, 'me'])->name('me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/perfil',  [PerfilController::class, 'perfil'])->name('perfil'); // alias de /me
});