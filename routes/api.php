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
use App\Http\Controllers\Api\DashboardController;

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
    Route::get('/perfil', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Ruta del Dashboard (Inicio)
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/notificaciones', [App\Http\Controllers\Api\NotificacionController::class, 'index']);
    Route::get('/notificaciones/unread', [App\Http\Controllers\Api\NotificacionController::class, 'unread']);
    Route::put('/notificaciones/{id}/read', [App\Http\Controllers\Api\NotificacionController::class, 'markAsRead']);
    Route::put('/notificaciones/read-all', [App\Http\Controllers\Api\NotificacionController::class, 'markAllAsRead']);

    // Mover aquí la ruta de pedidos para protegerla con autenticación
    Route::apiResource('pedidos', PedidoController::class)->except(['update', 'destroy']);
    Route::post('/pedidos/{id}/confirmar-pago', [PedidoController::class, 'confirmarPago']);
    Route::patch('/pedidos/{id}/estado', [PedidoController::class, 'actualizarEstado']);

    // Rutas protegidas para administración (Crear, Editar, Eliminar)
    // El controlador verificará si es Admin, Socio o Vendedor
    Route::apiResource('productos', ProductoController::class)->except(['index', 'show']);
    Route::apiResource('videos', VideoController::class)->except(['index', 'show']);
    Route::apiResource('premios', PremioController::class)->except(['index', 'show']);
    Route::apiResource('materiales', MaterialController::class)->except(['index', 'show']);

    // Rutas operativas protegidas (Requieren Auth)
    Route::apiResource('comisiones', ComisionController::class);
    Route::apiResource('historial-puntos', HistorialPuntosController::class)->except(['update', 'destroy']);
    Route::apiResource('canje-premios', CanjePremioController::class);
});

Route::apiResource('categorias', CategoriaController::class);
Route::apiResource('categoria-premios', CategoriaPremioController::class);
Route::apiResource('categoria-videos', CategoriaVideoController::class);
Route::apiResource('tipo-materiales', TipoMaterialController::class);

// Rutas públicas (Solo ver listados y detalles)
Route::apiResource('productos', ProductoController::class)->only(['index', 'show']);
Route::apiResource('premios', PremioController::class)->only(['index', 'show']);
Route::apiResource('videos', VideoController::class)->only(['index', 'show']);
Route::apiResource('materiales', MaterialController::class)->only(['index', 'show']);

Route::apiResource('configuraciones', ConfiguracionController::class);




// --- RUTAS DE AUTENTICACIÓN ---
Route::post('/registro', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::get('/version', function () {
    return response()->json([
        'version' => '1.0.0',
        'mensaje' => '¡Hola desde Producción! El despliegue funciona.'
    ]);
});