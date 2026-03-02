<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;

use App\Http\Requests\Categoria\StoreCategoriaRequest;
use App\Http\Requests\Categoria\UpdateCategoriaRequest;

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
    public function store(StoreCategoriaRequest $request) // <--- ¿CAMBIASTE ESTO?
    {
        // Usamos $request->validated() que devuelve solo los datos validados
        $categoria = Categoria::create($request->validated()); // <--- ¿Y ESTO?
        
        return response()->json([
            'message' => 'Categoría creada exitosamente',
            'data' => $categoria
        ], 201);
    }

    /**
     * Muestra la categoría especificada.
     */
    public function show(Categoria $categoria)
    {
        // Ya no necesitamos buscarla manualmente
        return response()->json($categoria, 200);
    }

    /**
     * Actualiza la categoría especificada en la base de datos.
     * * Usamos 'UpdateCategoriaRequest $request' y 'Categoria $categoria'
     */
    public function update(UpdateCategoriaRequest $request, Categoria $categoria)
    {
        // Ya no necesitamos buscarla.
        // Usamos $request->validated() para obtener solo los datos validados.
        $categoria->update($request->validated());

        return response()->json([
            'message' => 'Categoría actualizada exitosamente',
            'data' => $categoria
        ], 200);
    }

    /**
     * Elimina la categoría especificada de la base de datos.
     * * Usamos 'Categoria $categoria'
     */
    public function destroy(Categoria $categoria)
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        // Ya no necesitamos buscarla.
        $categoria->delete();

        return response()->json([
            'message' => 'Categoría eliminada exitosamente'
        ], 200);
    }
}