<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CanjePremio;
use App\Models\Premio;
use App\Models\HistorialPuntos;
use App\Http\Requests\CanjePremio\StoreCanjePremioRequest;
use App\Http\Requests\CanjePremio\UpdateCanjePremioRequest;
use Illuminate\Support\Facades\DB;

class CanjePremioController extends Controller
{
    public function index()
    {
        return response()->json(CanjePremio::with(['usuario', 'premio'])->latest()->get());
    }

    public function store(StoreCanjePremioRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $premio = Premio::findOrFail($request->premio_id);
                
                // Aquí deberías calcular el saldo actual del usuario (SUMA de historial)
                // Simplificado para el ejemplo:
                $puntosUsuario = HistorialPuntos::where('user_id', $request->user_id)->sum('puntos');
                
                if ($puntosUsuario < $premio->puntos_requeridos) {
                    throw new \Exception("Puntos insuficientes. Tienes $puntosUsuario y necesitas {$premio->puntos_requeridos}.");
                }

                // 1. Registrar Canje
                $canje = CanjePremio::create([
                    'user_id' => $request->user_id,
                    'premio_id' => $premio->id,
                    'puntos_utilizados' => $premio->puntos_requeridos,
                    'estado' => 'pendiente',
                    'observaciones' => $request->observaciones
                ]);

                // 2. Restar puntos en el historial
                HistorialPuntos::create([
                    'user_id' => $request->user_id,
                    'puntos' => -$premio->puntos_requeridos, // Negativo
                    'tipo' => 'egreso',
                    'descripcion' => 'Canje de premio: ' . $premio->nombre,
                    'balance_anterior' => $puntosUsuario,
                    'balance_nuevo' => $puntosUsuario - $premio->puntos_requeridos
                ]);
                
                // 3. Restar stock del premio
                $premio->decrement('stock');

                return response()->json(['message' => 'Canje realizado con éxito', 'data' => $canje], 201);
            });

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function show(CanjePremio $canjePremio)
    {
        return response()->json($canjePremio->load(['usuario', 'premio']));
    }

    public function update(UpdateCanjePremioRequest $request, CanjePremio $canjePremio)
    {
        $canjePremio->update($request->validated());
        return response()->json(['message' => 'Estado actualizado', 'data' => $canjePremio]);
    }
    
    public function destroy(CanjePremio $canjePremio)
    {
        $canjePremio->delete();
        return response()->json(['message' => 'Canje eliminado']);
    }
}