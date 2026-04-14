<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Premio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\Premio\StorePremioRequest;
use App\Http\Requests\Premio\UpdatePremioRequest;
use App\Traits\HandlesBase64Files;

class PremioController extends Controller
{
    use HandlesBase64Files;
    /**
     * Muestra una lista de los premios disponibles.
     */
    public function index(Request $request)
    {
        $perPage = min((int) $request->query('per_page', 15), 100);

        $premios = Premio::with('categoriaPremio')
            ->where('disponible', true)
            ->paginate($perPage);

        return response()->json($premios, 200);
    }

    /**
     * Almacena un nuevo premio.
     */
    public function store(StorePremioRequest $request)
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $datos = $request->validated();

        // Procesar imagen en Base64 si existe
        if ($request->filled('imagen')) {
            $path = $this->saveBase64File($request->input('imagen'), 'premios');
            if ($path) {
                $datos['imagen_url'] = $path;
            }
            unset($datos['imagen']);
        }

        $premio = Premio::create($datos);
        
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
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $datos = $request->validated();

        // Procesar imagen en Base64 si existe
        if ($request->filled('imagen')) {
            // Borrar imagen anterior si existe
            if ($premio->imagen_url && Storage::disk('public')->exists($premio->imagen_url)) {
                Storage::disk('public')->delete($premio->imagen_url);
            }
            
            $path = $this->saveBase64File($request->input('imagen'), 'premios');
            if ($path) {
                $datos['imagen_url'] = $path;
            }
            unset($datos['imagen']);
        }

        $premio->update($datos);

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
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $premio->delete();

        return response()->json([
            'message' => 'Premio eliminado exitosamente'
        ], 200);
    }
}