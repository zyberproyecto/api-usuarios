<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminUsuariosController;

/*
|--------------------------------------------------------------------------
| Preflight (CORS) — responde 204 a cualquier OPTIONS /api/*
|--------------------------------------------------------------------------
*/
Route::options('{any}', function () {
    return response()->noContent(); // 204
})->where('any', '.*');

/*
|--------------------------------------------------------------------------
| Endpoints públicos
|--------------------------------------------------------------------------
*/
Route::get('/health', fn () => ['ok' => true]);

// Login único (admin y socio) → devuelve token Sanctum con ability según rol
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Endpoints protegidos (requieren Bearer token Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Perfil y sesión
    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Bloque Admin: requiere además ability=admin
    Route::middleware('abilities:admin')->prefix('admin')->group(function () {
        Route::get('/usuarios/pendientes', [AdminUsuariosController::class, 'pendientes']);
        Route::post('/usuarios/{identificador}/estado', [AdminUsuariosController::class, 'setEstado']);
    });
});