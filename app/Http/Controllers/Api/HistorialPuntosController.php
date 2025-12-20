<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HistorialPuntos;
use App\Http\Requests\HistorialPuntos\StoreHistorialPuntosRequest;

class HistorialPuntosController extends Controller
{
    public function index()
    {
        return response()->json(HistorialPuntos::with('usuario:id,nombre_completo')->latest()->get());
    }

    public function store(StoreHistorialPuntosRequest $request)
    {
        $historial = HistorialPuntos::create($request->validated());
        return response()->json(['message' => 'Movimiento registrado', 'data' => $historial], 201);
    }
    
    public function show(HistorialPuntos $historialPunto) // Ojo con el nombre singular
    {
        return response()->json($historialPunto);
    }

    // No solemos permitir update/destroy en historiales contables
}
