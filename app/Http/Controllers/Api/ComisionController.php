<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comision;
use App\Notifications\Comisiones\ComisionGenerada;
use Illuminate\Http\Request;
use App\Http\Requests\Comision\StoreComisionRequest;
use App\Http\Requests\Comision\UpdateComisionRequest;

class ComisionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min((int) $request->query('per_page', 15), 100);

        $query = Comision::with(['vendedor:id,nombre_completo', 'comprador:id,nombre_completo'])->latest();

        if (! auth()->user()->isAdmin()) {
            $query->where('vendedor_id', auth()->id());
        }

        return response()->json($query->paginate($perPage));
    }

    public function store(StoreComisionRequest $request)
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $comision = Comision::create($request->validated());
        
        // Cargar las relaciones necesarias y disparar notificación al vendedor
        $comision->load(['vendedor', 'pedido.detalles.producto', 'comprador']);
        $comision->vendedor->notify(new ComisionGenerada($comision));

        return response()->json(['message' => 'Comisión registrada', 'data' => $comision], 201);
    }

    public function show(Comision $comision)
    {
        // Verificar propiedad si no es admin
        if (! auth()->user()->isAdmin() && $comision->vendedor_id !== auth()->id()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }
        return response()->json($comision->load(['vendedor', 'comprador', 'pedido']));
    }

    public function update(UpdateComisionRequest $request, Comision $comision)
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }
        $comision->update($request->validated());
        return response()->json(['message' => 'Comisión actualizada', 'data' => $comision]);
    }

    public function destroy(Comision $comision)
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }
        $comision->delete();
        return response()->json(['message' => 'Comisión eliminada']);
    }
}