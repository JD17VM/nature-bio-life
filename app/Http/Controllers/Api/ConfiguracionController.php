<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    /**
     * Muestra una lista de todas las configuraciones.
     */
    public function index()
    {
        $configuraciones = Configuracion::all();

        if ($configuraciones->isEmpty()) {
            return response()->json([
                'message' => 'No hay configuraciones registradas',
                'status' => 404
            ], 404);
        }

        return response()->json($configuraciones, 200);
    }

    /**
     * Almacena una nueva configuración.
     */
    public function store(Request $request)
    {
        $configuracion = Configuracion::create($request->all());
        
        return response()->json([
            'message' => 'Configuración creada exitosamente',
            'data' => $configuracion
        ], 201);
    }

    /**
     * Muestra la configuración especificada (buscando por ID o CLAVE).
     */
    public function show($id)
    {
        // Permitimos buscar por ID o por clave
        $configuracion = Configuracion::where('id', $id)->orWhere('clave', $id)->first();

        if (!$configuracion) {
            return response()->json([
                'message' => 'Configuración no encontrada',
                'status' => 404
            ], 404);
        }

        return response()->json($configuracion, 200);
    }

    /**
     * Actualiza la configuración especificada.
     */
    public function update(Request $request, $id)
    {
        $configuracion = Configuracion::where('id', $id)->orWhere('clave', $id)->first();

        if (!$configuracion) {
            return response()->json([
                'message' => 'Configuración no encontrada',
                'status' => 404
            ], 404);
        }

        $configuracion->update($request->all());

        return response()->json([
            'message' => 'Configuración actualizada exitosamente',
            'data' => $configuracion
        ], 200);
    }

    /**
     * Elimina la configuración especificada.
     */
    public function destroy($id)
    {
        $configuracion = Configuracion::where('id', $id)->orWhere('clave', $id)->first();

        if (!$configuracion) {
            return response()->json([
                'message' => 'Configuración no encontrada',
                'status' => 404
            ], 404);
        }

        $configuracion->delete();

        return response()->json([
            'message' => 'Configuración eliminada exitosamente'
        ], 200);
    }
}