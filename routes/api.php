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
use App\Http\Controllers\Api\ReferidoController;

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
    Route::put('/perfil', [AuthController::class, 'updateProfile']);
    Route::post('/cambiar-contraseña', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Ruta del Dashboard (Inicio)
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/notificaciones', [App\Http\Controllers\Api\NotificacionController::class, 'index']);
    Route::get('/notificaciones/unread', [App\Http\Controllers\Api\NotificacionController::class, 'unread']);
    Route::put('/notificaciones/{id}/read', [App\Http\Controllers\Api\NotificacionController::class, 'markAsRead']);
    Route::put('/notificaciones/read-all', [App\Http\Controllers\Api\NotificacionController::class, 'markAllAsRead']);

    // ============================================================
    // TRANSACCIONES Y DATOS PERSONALES (Todos los usuarios)
    // ============================================================
    Route::apiResource('pedidos', PedidoController::class)->except(['update', 'destroy']);
    Route::post('/pedidos/{id}/confirmar-pago', [PedidoController::class, 'confirmarPago']);

    // Referidos del usuario autenticado
    Route::get('/referidos', [ReferidoController::class, 'index']);
    Route::get('/referidos/{id}', [ReferidoController::class, 'show']);

    // Ver comisiones propias y historial propio
    Route::apiResource('comisiones', ComisionController::class)->only(['index', 'show']);
    Route::apiResource('historial-puntos', HistorialPuntosController::class)->only(['index', 'show']);
    Route::apiResource('canje-premios', CanjePremioController::class);

    // ============================================================
    // LECTURA DE CATÁLOGOS (Todos los usuarios autenticados)
    // ============================================================
    Route::apiResource('categorias', CategoriaController::class)->only(['index', 'show']);
    Route::apiResource('categoria-premios', CategoriaPremioController::class)->only(['index', 'show']);
    Route::apiResource('categoria-videos', CategoriaVideoController::class)->only(['index', 'show']);
    Route::apiResource('tipo-materiales', TipoMaterialController::class)->only(['index', 'show']);
    Route::apiResource('productos', ProductoController::class)->only(['index', 'show']);
    Route::apiResource('premios', PremioController::class)->only(['index', 'show']);
    Route::apiResource('videos', VideoController::class)->only(['index', 'show']);
    Route::apiResource('materiales', MaterialController::class)->only(['index', 'show']);
    Route::apiResource('configuraciones', ConfiguracionController::class)->only(['index', 'show']);
});

// ============================================================
// RUTAS SOLO ADMIN (Crear, Editar, Eliminar recursos)
// ============================================================
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Gestión de productos
    Route::apiResource('productos', ProductoController::class)->except(['index', 'show']);

    // Gestión de videos
    Route::apiResource('videos', VideoController::class)->except(['index', 'show']);

    // Gestión de premios
    Route::apiResource('premios', PremioController::class)->except(['index', 'show']);

    // Gestión de materiales
    Route::apiResource('materiales', MaterialController::class)->except(['index', 'show']);

    // Gestión de categorías
    Route::apiResource('categorias', CategoriaController::class)->except(['index', 'show']);
    Route::apiResource('categoria-premios', CategoriaPremioController::class)->except(['index', 'show']);
    Route::apiResource('categoria-videos', CategoriaVideoController::class)->except(['index', 'show']);
    Route::apiResource('tipo-materiales', TipoMaterialController::class)->except(['index', 'show']);

    // Gestión de configuraciones
    Route::apiResource('configuraciones', ConfiguracionController::class)->except(['index', 'show']);

    // Gestión de pedidos (cambiar estado)
    Route::patch('/pedidos/{id}/estado', [PedidoController::class, 'actualizarEstado']);

    // Gestión de comisiones (crear, editar, eliminar)
    Route::apiResource('comisiones', ComisionController::class)->except(['index', 'show']);
    Route::post('/comisiones', [ComisionController::class, 'store']);
    Route::put('/comisiones/{comision}', [ComisionController::class, 'update']);
    Route::delete('/comisiones/{comision}', [ComisionController::class, 'destroy']);

    // Movimientos manuales de puntos
    Route::apiResource('historial-puntos', HistorialPuntosController::class)->only(['store']);

    // Aprobar/rechazar canjes de premios
    Route::put('/canje-premios/{canjePremio}', [CanjePremioController::class, 'update']);
    Route::delete('/canje-premios/{canjePremio}', [CanjePremioController::class, 'destroy']);
});




// ============================================================
// PÚBLICAS (Sin autenticación - Solo Login/Registro)
// ============================================================
Route::post('/registro', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::get('/version', function () {
    return response()->json([
        'version' => '1.0.0',
        'mensaje' => '¡Hola desde Producción! El despliegue funciona.'
    ]);
});