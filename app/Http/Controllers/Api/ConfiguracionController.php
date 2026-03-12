<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;
// Importamos los nuevos Form Requests
use App\Http\Requests\Configuracion\StoreConfiguracionRequest;
use App\Http\Requests\Configuracion\UpdateConfiguracionRequest;

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
    public function store(StoreConfiguracionRequest $request)
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $configuracion = Configuracion::create($request->validated());
        
        return response()->json([
            'message' => 'Configuración creada exitosamente',
            'data' => $configuracion
        ], 201);
    }

    /**
     * Muestra la configuración especificada.
     * Usamos Route Model Binding ($configuracione)
     */
    public function show(Configuracion $configuracione)
    {
        return response()->json($configuracione, 200);
    }

    /**
     * Actualiza la configuración especificada.
     * * Nota: En la versión anterior permitíamos buscar por clave.
     * Ahora, con Route Model Binding, solo se buscará por ID,
     * lo cual es la práctica estándar de un apiResource.
     */
    public function update(UpdateConfiguracionRequest $request, Configuracion $configuracione)
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $configuracione->update($request->validated());

        return response()->json([
            'message' => 'Configuración actualizada exitosamente',
            'data' => $configuracione
        ], 200);
    }

    /**
     * Elimina la configuración especificada.
     */
    public function destroy(Configuracion $configuracione)
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $configuracione->delete();

        return response()->json([
            'message' => 'Configuración eliminada exitosamente'
        ], 200);
    }
}