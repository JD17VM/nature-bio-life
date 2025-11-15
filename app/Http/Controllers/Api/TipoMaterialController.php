<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoMaterial;
use Illuminate\Http\Request;

use App\Http\Requests\TipoMaterial\StoreTipoMaterialRequest;
use App\Http\Requests\TipoMaterial\UpdateTipoMaterialRequest;

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
    public function store(StoreTipoMaterialRequest $request)
    {
        $tipo = TipoMaterial::create($request->validated());
        
        return response()->json([
            'message' => 'Tipo de material creado exitosamente',
            'data' => $tipo
        ], 201);
    }

    /**
     * Muestra el tipo de material especificado.
     */
    public function show(TipoMaterial $tipoMateriale) // El parámetro debe coincidir con el nombre de la ruta
    {
        return response()->json($tipoMateriale, 200);
    }

    /**
     * Actualiza el tipo de material especificado.
     */
    public function update(UpdateTipoMaterialRequest $request, TipoMaterial $tipoMateriale)
    {
        $tipoMateriale->update($request->validated());

        return response()->json([
            'message' => 'Tipo de material actualizado exitosamente',
            'data' => $tipoMateriale
        ], 200);
    }

    /**
     * Elimina el tipo de material especificado.
     */
    public function destroy(TipoMaterial $tipoMateriale)
    {
        $tipoMateriale->delete();

        return response()->json([
            'message' => 'Tipo de material eliminado exitosamente'
        ], 200);
    }
}