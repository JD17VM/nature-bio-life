<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comision;
use App\Http\Requests\Comision\StoreComisionRequest;
use App\Http\Requests\Comision\UpdateComisionRequest;

class ComisionController extends Controller
{
    public function index()
    {
        $comisiones = Comision::with(['vendedor:id,nombre_completo', 'comprador:id,nombre_completo'])
            ->latest()
            ->get();
        return response()->json($comisiones);
    }

    public function store(StoreComisionRequest $request)
    {
        $comision = Comision::create($request->validated());
        return response()->json(['message' => 'Comisión registrada', 'data' => $comision], 201);
    }

    public function show(Comision $comision)
    {
        return response()->json($comision->load(['vendedor', 'comprador', 'pedido']));
    }

    public function update(UpdateComisionRequest $request, Comision $comision)
    {
        $comision->update($request->validated());
        return response()->json(['message' => 'Comisión actualizada', 'data' => $comision]);
    }

    public function destroy(Comision $comision)
    {
        $comision->delete();
        return response()->json(['message' => 'Comisión eliminada']);
    }
}