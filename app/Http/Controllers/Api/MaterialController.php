<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;

use App\Http\Requests\Material\StoreMaterialRequest;
use App\Http\Requests\Material\UpdateMaterialRequest;

class MaterialController extends Controller
{
    /**
     * Muestra una lista de los materiales activos.
     */
    public function index()
    {
        $materiales = Material::with('tipoMaterial')->where('activo', true)->get();

        if ($materiales->isEmpty()) {
            return response()->json([
                'message' => 'No hay materiales registrados',
                'status' => 404
            ], 404);
        }

        return response()->json($materiales, 200);
    }

    /**
     * Almacena un nuevo material.
     */
    public function store(StoreMaterialRequest $request)
    {
        $material = Material::create($request->validated());
        
        return response()->json([
            'message' => 'Material creado exitosamente',
            'data' => $material
        ], 201);
    }

    /**
     * Muestra el material especificado.
     * Usamos Route Model Binding ($materiale)
     */
    public function show(Material $materiale)
    {
        $materiale->load('tipoMaterial');
        return response()->json($materiale, 200);
    }

    /**
     * Actualiza el material especificado.
     */
    public function update(UpdateMaterialRequest $request, Material $materiale)
    {
        $materiale->update($request->validated());

        return response()->json([
            'message' => 'Material actualizado exitosamente',
            'data' => $materiale
        ], 200);
    }

    /**
     * Elimina el material especificado.
     */
    public function destroy(Material $materiale)
    {
        $materiale->delete();

        return response()->json([
            'message' => 'Material eliminado exitosamente'
        ], 200);
    }
}