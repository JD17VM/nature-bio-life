<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Premio;
use Illuminate\Http\Request;

use App\Http\Requests\Premio\StorePremioRequest;
use App\Http\Requests\Premio\UpdatePremioRequest;

class PremioController extends Controller
{
    /**
     * Muestra una lista de los premios disponibles.
     */
    public function index()
    {
        $premios = Premio::with('categoriaPremio')->where('disponible', true)->get();

        if ($premios->isEmpty()) {
            return response()->json([
                'message' => 'No hay premios disponibles',
                'status' => 404
            ], 404);
        }

        return response()->json($premios, 200);
    }

    /**
     * Almacena un nuevo premio.
     */
    public function store(StorePremioRequest $request)
    {
        $premio = Premio::create($request->validated());
        
        return response()->json([
            'message' => 'Premio creado exitosamente',
            'data' => $premio
        ], 201);
    }

    /**
     * Muestra el premio especificado.
     */
    public function show(Premio $premio)
    {
        $premio->load('categoriaPremio');
        return response()->json($premio, 200);
    }

    /**
     * Actualiza el premio especificado.
     */
    public function update(UpdatePremioRequest $request, Premio $premio)
    {
        $premio->update($request->validated());

        return response()->json([
            'message' => 'Premio actualizado exitosamente',
            'data' => $premio
        ], 200);
    }

    /**
     * Elimina el premio especificado.
     */
    public function destroy(Premio $premio)
    {
        $premio->delete();

        return response()->json([
            'message' => 'Premio eliminado exitosamente'
        ], 200);
    }
}