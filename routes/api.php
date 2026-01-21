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
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\ComisionController;
use App\Http\Controllers\Api\HistorialPuntosController;
use App\Http\Controllers\Api\CanjePremioController;

use App\Http\Controllers\Api\AuthController;

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

Route::middleware('auth:sanctum')->group(function () {
    // Ruta de prueba de perfil
    Route::get('/perfil', [AuthController::class, 'profile']);

    Route::get('/notificaciones', [App\Http\Controllers\Api\NotificacionController::class, 'index']);
    Route::get('/notificaciones/unread', [App\Http\Controllers\Api\NotificacionController::class, 'unread']);
    Route::put('/notificaciones/{id}/read', [App\Http\Controllers\Api\NotificacionController::class, 'markAsRead']);
    Route::put('/notificaciones/read-all', [App\Http\Controllers\Api\NotificacionController::class, 'markAllAsRead']);
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
Route::apiResource('pedidos', PedidoController::class);
Route::apiResource('comisiones', ComisionController::class);
Route::apiResource('historial-puntos', HistorialPuntosController::class)->except(['update', 'destroy']);
Route::apiResource('canje-premios', CanjePremioController::class);




// --- RUTAS DE AUTENTICACIÓN ---
Route::post('/registro', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::get('/version', function () {
    return response()->json([
        'version' => '1.0.0',
        'mensaje' => '¡Hola desde Producción! El despliegue funciona.'
    ]);
});