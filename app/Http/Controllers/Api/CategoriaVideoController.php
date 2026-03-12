<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoriaVideo;
use Illuminate\Http\Request;

use App\Http\Requests\CategoriaVideo\StoreCategoriaVideoRequest;
use App\Http\Requests\CategoriaVideo\UpdateCategoriaVideoRequest;

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
    public function store(StoreCategoriaVideoRequest $request)
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $categoria = CategoriaVideo::create($request->validated());
        
        return response()->json([
            'message' => 'Categoría de video creada exitosamente',
            'data' => $categoria
        ], 201);
    }

    /**
     * Muestra la categoría de video especificada.
     */
    public function show(CategoriaVideo $categoriaVideo)
    {
        return response()->json($categoriaVideo, 200);
    }

    /**
     * Actualiza la categoría de video especificada.
     */
    public function update(UpdateCategoriaVideoRequest $request, CategoriaVideo $categoriaVideo)
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $categoriaVideo->update($request->validated());

        return response()->json([
            'message' => 'Categoría de video actualizada exitosamente',
            'data' => $categoriaVideo
        ], 200);
    }

    /**
     * Elimina la categoría de video especificada.
     */
    public function destroy(CategoriaVideo $categoriaVideo)
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $categoriaVideo->delete();

        return response()->json([
            'message' => 'Categoría de video eliminada exitosamente'
        ], 200);
    }
}