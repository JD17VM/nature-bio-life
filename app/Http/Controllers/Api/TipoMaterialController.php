<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoMaterial;
use Illuminate\Http\Request;

class TipoMaterialController extends Controller
{
    /**
     * Muestra una lista de los tipos de material.
     */
    public function index()
    {
        $tipos = TipoMaterial::all();

        if ($tipos->isEmpty()) {
            return response()->json([
                'message' => 'No hay tipos de material registrados',
                'status' => 404
            ], 404);
        }

        return response()->json($tipos, 200);
    }

    /**
     * Almacena un nuevo tipo de material.
     */
    public function store(Request $request)
    {
        $tipo = TipoMaterial::create($request->all());
        
        return response()->json([
            'message' => 'Tipo de material creado exitosamente',
            'data' => $tipo
        ], 201);
    }

    /**
     * Muestra el tipo de material especificado.
     */
    public function show($id)
    {
        $tipo = TipoMaterial::find($id);

        if (!$tipo) {
            return response()->json([
                'message' => 'Tipo de material no encontrado',
                'status' => 404
            ], 404);
        }

        return response()->json($tipo, 200);
    }

    /**
     * Actualiza el tipo de material especificado.
     */
    public function update(Request $request, $id)
    {
        $tipo = TipoMaterial::find($id);

        if (!$tipo) {
            return response()->json([
                'message' => 'Tipo de material no encontrado',
                'status' => 404
            ], 404);
        }

        $tipo->update($request->all());

        return response()->json([
            'message' => 'Tipo de material actualizado exitosamente',
            'data' => $tipo
        ], 200);
    }

    /**
     * Elimina el tipo de material especificado.
     */
    public function destroy($id)
    {
        $tipo = TipoMaterial::find($id);

        if (!$tipo) {
            return response()->json([
                'message' => 'Tipo de material no encontrado',
                'status' => 404
            ], 404);
        }

        $tipo->delete();

        return response()->json([
            'message' => 'Tipo de material eliminado exitosamente'
        ], 200);
    }
}