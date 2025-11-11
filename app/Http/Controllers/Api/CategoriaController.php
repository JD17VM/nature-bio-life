<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Muestra una lista de las categorías activas.
     */
    public function index()
    {
        $categorias = Categoria::where('activa', true)->get();

        if ($categorias->isEmpty()) {
            return response()->json([
                'message' => 'No hay categorías registradas',
                'status' => 404
            ], 404);
        }

        return response()->json($categorias, 200);
    }

    /**
     * Almacena una nueva categoría en la base de datos.
     */
    public function store(Request $request)
    {
        // Nota: Agregaremos validación en la siguiente fase
        $categoria = Categoria::create($request->all());
        
        return response()->json([
            'message' => 'Categoría creada exitosamente',
            'data' => $categoria
        ], 201);
    }

    /**
     * Muestra la categoría especificada.
     */
    public function show($id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoría no encontrada',
                'status' => 404
            ], 404);
        }

        return response()->json($categoria, 200);
    }

    /**
     * Actualiza la categoría especificada en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoría no encontrada',
                'status' => 404
            ], 404);
        }

        $categoria->update($request->all());

        return response()->json([
            'message' => 'Categoría actualizada exitosamente',
            'data' => $categoria
        ], 200);
    }

    /**
     * Elimina la categoría especificada de la base de datos.
     */
    public function destroy($id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoría no encontrada',
                'status' => 404
            ], 404);
        }

        // Aquí podríamos validar si tiene productos asociados antes de borrar
        $categoria->delete();

        return response()->json([
            'message' => 'Categoría eliminada exitosamente'
        ], 200);
    }
}