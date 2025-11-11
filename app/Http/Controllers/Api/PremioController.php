<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Premio;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
        $premio = Premio::create($request->all());
        
        return response()->json([
            'message' => 'Premio creado exitosamente',
            'data' => $premio
        ], 201);
    }

    /**
     * Muestra el premio especificado.
     */
    public function show($id)
    {
        $premio = Premio::with('categoriaPremio')->find($id);

        if (!$premio) {
            return response()->json([
                'message' => 'Premio no encontrado',
                'status' => 404
            ], 404);
        }

        return response()->json($premio, 200);
    }

    /**
     * Actualiza el premio especificado.
     */
    public function update(Request $request, $id)
    {
        $premio = Premio::find($id);

        if (!$premio) {
            return response()->json([
                'message' => 'Premio no encontrado',
                'status' => 404
            ], 404);
        }

        $premio->update($request->all());

        return response()->json([
            'message' => 'Premio actualizado exitosamente',
            'data' => $premio
        ], 200);
    }

    /**
     * Elimina el premio especificado.
     */
    public function destroy($id)
    {
        $premio = Premio::find($id);

        if (!$premio) {
            return response()->json([
                'message' => 'Premio no encontrado',
                'status' => 404
            ], 404);
        }

        $premio->delete();

        return response()->json([
            'message' => 'Premio eliminado exitosamente'
        ], 200);
    }
}