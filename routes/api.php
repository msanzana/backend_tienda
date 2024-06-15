<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\SucursalController;
use App\Http\Controllers\Api\TrabajadorController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [UserController::class, 'createUser']);
Route::post('/login', [UserController::class, 'loginUser']);

// Permitir POST en /usuario sin autenticación
Route::post('/usuario', [UsuarioController::class, 'store']);

// Rutas de /usuario que requieren autenticación, excepto POST
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/trabajador', TrabajadorController::class);
    Route::resource('/sucursales', SucursalController::class);
});
