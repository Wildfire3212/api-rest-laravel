<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pedidoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClienteController;

use App\Http\Middleware\ApiAuthMiddleWare;


use App\Pedido;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



//RUTAS DEL API

Route::post('/api/register', [UserController::class, 'register']);
Route::post('/api/login', [UserController::class, 'login']);
Route::put('/api/update', [UserController::class, 'update'])->middleware(ApiAuthMiddleWare::class);

//Rutas de cliente

Route::resource('/cliente', ClienteController::class);

//Rutas de los pedidos

Route::resource('/pedidos', PedidoController::class);
