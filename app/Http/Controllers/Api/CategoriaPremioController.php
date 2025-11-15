<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoriaPremio;
use Illuminate\Http\Request;

use App\Http\Requests\CategoriaPremio\StoreCategoriaPremioRequest;
use App\Http\Requests\CategoriaPremio\UpdateCategoriaPremioRequest;

class CategoriaPremioController extends Controller
{
    /**
     * Muestra una lista de las categorías de premios.
     */
    public function index()
    {
        $categorias = CategoriaPremio::all(); // Esta tabla no tiene campo 'activa'

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
     * Usamos nuestro Form Request para validación automática.
     */
    public function store(StoreCategoriaPremioRequest $request)
    {
        $categoria = CategoriaPremio::create($request->validated());
        
        return response()->json([
            'message' => 'Categoría de premio creada exitosamente',
            'data' => $categoria
        ], 201);
    }

    /**
     * Muestra la categoría de premio especificada.
     * Usamos Route Model Binding (CategoriaPremio $categoriaPremio).
     * Nota: Laravel es inteligente. Como nuestra ruta es 'categoria-premios',
     * automáticamente buscará un parámetro llamado 'categoria_premio'.
     */
    public function show(CategoriaPremio $categoriaPremio)
    {
        return response()->json($categoriaPremio, 200);
    }

    /**
     * Actualiza la categoría de premio especificada.
     */
    public function update(UpdateCategoriaPremioRequest $request, CategoriaPremio $categoriaPremio)
    {
        $categoriaPremio->update($request->validated());

        return response()->json([
            'message' => 'Categoría de premio actualizada exitosamente',
            'data' => $categoriaPremio
        ], 200);
    }

    /**
     * Elimina la categoría de premio especificada.
     */
    public function destroy(CategoriaPremio $categoriaPremio)
    {
        $categoriaPremio->delete();

        return response()->json([
            'message' => 'Categoría de premio eliminada exitosamente'
        ], 200);
    }
}