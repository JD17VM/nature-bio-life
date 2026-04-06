<?php

namespace App\Http\Controllers\Api;

use App\Enums\EstadoComisionEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Comision;
use Illuminate\Http\Request;

class ReferidoController extends Controller
{
    /**
     * Lista todos los referidos directos del usuario autenticado.
     * GET /api/referidos
     */
    public function index(Request $request)
    {
        $user    = $request->user();
        $perPage = min((int) $request->query('per_page', 15), 100);

        // Si es admin puede ver todos los referidos de cualquier patrocinador
        $patrocinadorId = $user->isAdmin()
            ? $request->query('patrocinador_id', $user->id)
            : $user->id;

        $referidos = User::where('patrocinador_id', $patrocinadorId)
            ->withCount('referidos as total_sub_referidos')    // cuántos referidos tiene este referido
            ->withCount('pedidos as total_pedidos')
            ->withSum('pedidos as total_compras', 'total')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Para cada referido calculamos cuánto nos ha generado en comisiones
        $referidos->getCollection()->transform(function ($referido) use ($user) {
            $referido->total_comisiones_generadas = Comision::where('vendedor_id', $user->id)
                ->where('comprador_id', $referido->id)
                ->sum('monto_comision');

            return $referido;
        });

        return response()->json($referidos);
    }

    /**
     * Muestra el detalle de un referido específico con sus pedidos y comisiones generadas.
     * GET /api/referidos/{id}
     */
    public function show(Request $request, $id)
    {
        $user     = $request->user();
        $referido = User::withCount('pedidos as total_pedidos')
            ->withSum('pedidos as total_compras', 'total')
            ->findOrFail($id);

        // Solo puede ver el detalle si es su referido directo o si es admin
        if (!$user->isAdmin() && $referido->patrocinador_id !== $user->id) {
            return response()->json([
                'message' => 'Este usuario no es tu referido.'
            ], 403);
        }

        // Pedidos del referido (últimos 10, paginados)
        $perPage = min((int) $request->query('per_page', 10), 50);
        $pedidos = $referido->pedidos()
            ->with('detalles.producto')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Comisiones que me generó este referido
        $comisiones = Comision::where('vendedor_id', $user->id)
            ->where('comprador_id', $referido->id)
            ->with('pedido:id,numero_pedido,total,estado,created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'referido' => [
                'id'              => $referido->id,
                'nombre_completo' => $referido->nombre_completo,
                'email'           => $referido->email,
                'telefono'        => $referido->telefono,
                'rol'             => $referido->rol->value,
                'rol_label'       => $referido->rol->label(),
                'codigo_referido' => $referido->codigo_referido,
                'activo'          => $referido->activo,
                'miembro_desde'   => $referido->created_at,
            ],
            'resumen' => [
                'total_pedidos'              => $referido->total_pedidos,
                'total_compras'              => (float) ($referido->total_compras ?? 0),
                'total_comisiones_generadas' => (float) $comisiones->sum('monto_comision'),
                'comisiones_pendientes'      => (float) $comisiones
                    ->filter(fn($c) => $c->estado === EstadoComisionEnum::PENDIENTE)
                    ->sum('monto_comision'),
                'comisiones_pagadas'         => (float) $comisiones
                    ->filter(fn($c) => $c->estado === EstadoComisionEnum::PAGADA)
                    ->sum('monto_comision'),
            ],
            'pedidos'    => $pedidos,
            'comisiones' => $comisiones,
        ]);
    }
}
