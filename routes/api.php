<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TrabajadorController;
use App\Http\Controllers\Api\UsuarioController;
use App\Models\Trabajadores;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/register', [UserController::class, 'createUser']);
Route::post('/login', [UserController::class, 'loginUser']);

//Route::group(['middleware' => ['auth:api']], function() {
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/usuario', UsuarioController::class);
    Route::Resource('/trabajador',TrabajadorController::class);
});
