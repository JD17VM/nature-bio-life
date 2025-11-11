<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\CategoriaPremioController;
use App\Http\Controllers\Api\CategoriaVideoController;
use App\Http\Controllers\Api\TipoMaterialController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\PremioController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\ConfiguracionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::apiResource('categorias', CategoriaController::class);
Route::apiResource('categoria-premios', CategoriaPremioController::class);
Route::apiResource('categoria-videos', CategoriaVideoController::class);
Route::apiResource('tipo-materiales', TipoMaterialController::class);

Route::apiResource('productos', ProductoController::class);
Route::apiResource('premios', PremioController::class);
Route::apiResource('videos', VideoController::class);
Route::apiResource('materiales', MaterialController::class);
Route::apiResource('configuraciones', ConfiguracionController::class);