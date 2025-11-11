<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoriaPremio;
use Illuminate\Http\Request;

class CategoriaPremioController extends Controller
{
    /**
     * Muestra una lista de las categorías de premios.
     */
    public function index()
    {
        $categorias = CategoriaPremio::all(); // No tiene 'activa'

        if ($categorias->isEmpty()) {
            return response()->json([
                'message' => 'No hay categorías de premios registradas',
                'status' => 404
            ], 404);
        }

        return response()->json($categorias, 200);
    }

    /**
     * Almacena una nueva categoría de premio.
     */
    public function store(Request $request)
    {
        $categoria = CategoriaPremio::create($request->all());
        
        return response()->json([
            'message' => 'Categoría de premio creada exitosamente',
            'data' => $categoria
        ], 201);
    }

    /**
     * Muestra la categoría de premio especificada.
     */
    public function show($id)
    {
        $categoria = CategoriaPremio::find($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoría de premio no encontrada',
                'status' => 404
            ], 404);
        }

        return response()->json($categoria, 200);
    }

    /**
     * Actualiza la categoría de premio especificada.
     */
    public function update(Request $request, $id)
    {
        $categoria = CategoriaPremio::find($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoría de premio no encontrada',
                'status' => 404
            ], 404);
        }

        $categoria->update($request->all());

        return response()->json([
            'message' => 'Categoría de premio actualizada exitosamente',
            'data' => $categoria
        ], 200);
    }

    /**
     * Elimina la categoría de premio especificada.
     */
    public function destroy($id)
    {
        $categoria = CategoriaPremio::find($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoría de premio no encontrada',
                'status' => 404
            ], 404);
        }

        $categoria->delete();

        return response()->json([
            'message' => 'Categoría de premio eliminada exitosamente'
        ], 200);
    }
}