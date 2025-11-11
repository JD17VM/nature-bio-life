<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
        $material = Material::create($request->all());
        
        return response()->json([
            'message' => 'Material creado exitosamente',
            'data' => $material
        ], 201);
    }

    /**
     * Muestra el material especificado.
     */
    public function show($id)
    {
        $material = Material::with('tipoMaterial')->find($id);

        if (!$material) {
            return response()->json([
                'message' => 'Material no encontrado',
                'status' => 404
            ], 404);
        }

        return response()->json($material, 200);
    }

    /**
     * Actualiza el material especificado.
     */
    public function update(Request $request, $id)
    {
        $material = Material::find($id);

        if (!$material) {
            return response()->json([
                'message' => 'Material no encontrado',
                'status' => 404
            ], 404);
        }

        $material->update($request->all());

        return response()->json([
            'message' => 'Material actualizado exitosamente',
            'data' => $material
        ], 200);
    }

    /**
     * Elimina el material especificado.
     */
    public function destroy($id)
    {
        $material = Material::find($id);

        if (!$material) {
            return response()->json([
                'message' => 'Material no encontrado',
                'status' => 404
            ], 404);
        }

        $material->delete();

        return response()->json([
            'message' => 'Material eliminado exitosamente'
        ], 200);
    }
}