<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comision;
use App\Models\HistorialPuntos;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Muestra el resumen principal para el usuario (Dashboard).
     * GET /api/dashboard
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // 1. Calcular Ganancias del Mes Actual
        // Sumamos las comisiones generadas en el mes y año actual
        $gananciasMes = Comision::where('vendedor_id', $user->id)
            ->whereMonth('fecha_generacion', Carbon::now()->month)
            ->whereYear('fecha_generacion', Carbon::now()->year)
            ->where('estado', '!=', 'anulada') // Excluir anuladas si aplica
            ->sum('monto_comision');

        // 2. Obtener Puntos Actuales (Balance)
        // Buscamos el último movimiento en el historial para obtener el saldo final
        $ultimoPunto = HistorialPuntos::where('user_id', $user->id)
            ->latest('id')
            ->first();
            
        $puntosActuales = $ultimoPunto ? $ultimoPunto->balance_nuevo : 0;

        // 3. Construir Respuesta
        return response()->json([
            'user' => [
                'name' => $user->nombre_completo,
                'rank' => ucfirst($user->rol), // Usamos el Rol como "Rango" (ej: Cliente, Patrocinador)
                'status' => $user->activo ? 'ACTIVO' : 'INACTIVO',
            ],
            'stats' => [
                'earnings' => (float) $gananciasMes, // Ganancias Mes
                'points' => $puntosActuales,         // Puntos Totales (Balance)
            ],
            'referral_link' => $user->codigo_referido ? url('/registro?ref=' . $user->codigo_referido) : null,
            'referral_code' => $user->codigo_referido // Enviamos también el código solo por si acaso
        ]);
    }
}