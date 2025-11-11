<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoriaVideo;
use Illuminate\Http\Request;

class CategoriaVideoController extends Controller
{
    /**
     * Muestra una lista de las categorías de videos.
     */
    public function index()
    {
        $categorias = CategoriaVideo::orderBy('orden', 'asc')->get();

        if ($categorias->isEmpty()) {
            return response()->json([
                'message' => 'No hay categorías de videos registradas',
                'status' => 404
            ], 404);
        }

        return response()->json($categorias, 200);
    }

    /**
     * Almacena una nueva categoría de video.
     */
    public function store(Request $request)
    {
        $categoria = CategoriaVideo::create($request->all());
        
        return response()->json([
            'message' => 'Categoría de video creada exitosamente',
            'data' => $categoria
        ], 201);
    }

    /**
     * Muestra la categoría de video especificada.
     */
    public function show($id)
    {
        $categoria = CategoriaVideo::find($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoría de video no encontrada',
                'status' => 404
            ], 404);
        }

        return response()->json($categoria, 200);
    }

    /**
     * Actualiza la categoría de video especificada.
     */
    public function update(Request $request, $id)
    {
        $categoria = CategoriaVideo::find($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoría de video no encontrada',
                'status' => 404
            ], 404);
        }

        $categoria->update($request->all());

        return response()->json([
            'message' => 'Categoría de video actualizada exitosamente',
            'data' => $categoria
        ], 200);
    }

    /**
     * Elimina la categoría de video especificada.
     */
    public function destroy($id)
    {
        $categoria = CategoriaVideo::find($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoría de video no encontrada',
                'status' => 404
            ], 404);
        }

        $categoria->delete();

        return response()->json([
            'message' => 'Categoría de video eliminada exitosamente'
        ], 200);
    }
}