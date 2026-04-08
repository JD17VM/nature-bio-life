<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HistorialPuntos;
use App\Http\Requests\HistorialPuntos\StoreHistorialPuntosRequest;
use Illuminate\Http\Request;

class HistorialPuntosController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min((int) $request->query('per_page', 15), 100);

        $query = HistorialPuntos::with('usuario:id,nombre_completo')->latest();

        if (! auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        return response()->json($query->paginate($perPage));
    }

    public function store(StoreHistorialPuntosRequest $request)
    {
        // Solo admin puede crear movimientos manuales de puntos
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $historial = HistorialPuntos::create($request->validated());
        return response()->json(['message' => 'Movimiento registrado', 'data' => $historial], 201);
    }
    
    public function show(HistorialPuntos $historialPunto)
    {
        if (! auth()->user()->isAdmin() && $historialPunto->user_id !== auth()->id()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }
        return response()->json($historialPunto);
    }

    // No solemos permitir update/destroy en historiales contables
}
