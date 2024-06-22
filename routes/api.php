<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VentaController;
use App\Http\Controllers\Api\CargosController;
use App\Http\Controllers\Api\CompraController;
use App\Http\Controllers\Api\KardexController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\SucursalController;
use App\Http\Controllers\Api\ProductosController;
use App\Http\Controllers\Api\TrabajadorController;
use App\Http\Controllers\Api\ProveedoresController;
use App\Http\Controllers\Api\SucursalesHasTrabajadoresController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [UserController::class, 'createUser']);
Route::post('/login', [UserController::class, 'loginUser']);

// Permitir POST en /usuario sin autenticación
Route::post('/usuario', [UsuarioController::class, 'store']);
Route::resource('/sucursales', SucursalController::class);

// Rutas de /usuario que requieren autenticación, excepto POST
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/trabajador', TrabajadorController::class);
    Route::get('/usuario', [UsuarioController::class, 'index']);
    Route::resource('/proveedores',ProveedoresController::class);
    Route::resource('/sucursales_trabajadores', SucursalesHasTrabajadoresController::class);
    Route::resource('/clientes',ClienteController::class);
    Route::resource('/cargos',CargosController::class);
    Route::resource('/productos', ProductosController::class);
    Route::resource('/compras',CompraController::class);
    Route::resource('/kardex', KardexController::class);
    Route::resource('/ventas',VentaController::class);
});
