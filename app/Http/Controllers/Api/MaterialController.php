<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\Material\StoreMaterialRequest;
use App\Http\Requests\Material\UpdateMaterialRequest;

class MaterialController extends Controller
{
    /**
     * Muestra una lista de los materiales activos.
     */
    public function index(Request $request)
    {
        $perPage = min((int) $request->query('per_page', 15), 100);

        $materiales = Material::with('tipoMaterial')
            ->where('activo', true)
            ->paginate($perPage);

        return response()->json($materiales, 200);
    }

    /**
     * Almacena un nuevo material.
     */
    public function store(StoreMaterialRequest $request)
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $datos = $request->validated();

        if ($request->hasFile('archivo')) {
            $path = $request->file('archivo')->store('materiales', 'public');
            $datos['archivo_url'] = $path;
            unset($datos['archivo']);
        }

        $material = Material::create($datos);
        
        return response()->json([
            'message' => 'Material creado exitosamente',
            'data' => $material
        ], 201);
    }

    /**
     * Muestra el material especificado.
     * Usamos Route Model Binding ($materiale)
     */
    public function show(Material $materiale)
    {
        $materiale->load('tipoMaterial');
        return response()->json($materiale, 200);
    }

    /**
     * Actualiza el material especificado.
     */
    public function update(UpdateMaterialRequest $request, Material $materiale)
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $datos = $request->validated();

        if ($request->hasFile('archivo')) {
            if ($materiale->archivo_url && Storage::disk('public')->exists($materiale->archivo_url)) {
                Storage::disk('public')->delete($materiale->archivo_url);
            }
            $path = $request->file('archivo')->store('materiales', 'public');
            $datos['archivo_url'] = $path;
            unset($datos['archivo']);
        }

        $materiale->update($datos);

        return response()->json([
            'message' => 'Material actualizado exitosamente',
            'data' => $materiale
        ], 200);
    }

    /**
     * Elimina el material especificado.
     */
    public function destroy(Material $materiale)
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        if ($materiale->archivo_url && Storage::disk('public')->exists($materiale->archivo_url)) {
            Storage::disk('public')->delete($materiale->archivo_url);
        }

        $materiale->delete();

        return response()->json([
            'message' => 'Material eliminado exitosamente'
        ], 200);
    }
}