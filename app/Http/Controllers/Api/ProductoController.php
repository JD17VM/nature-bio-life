<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\Producto\StoreProductoRequest;
use App\Http\Requests\Producto\UpdateProductoRequest;

class ProductoController extends Controller
{
    /**
     * Muestra una lista de los productos activos.
     */
    public function index()
    {
        // Eager loading de la relación 'categoria'
        $productos = Producto::with('categoria')->where('activo', true)->get();

        if ($productos->isEmpty()) {
            return response()->json([
                'message' => 'No hay productos registrados',
                'status' => 404
            ], 404);
        }

        return response()->json($productos, 200);
    }

    /**
     * Almacena un nuevo producto.
     */
    public function store(StoreProductoRequest $request)
    {
        $datos = $request->validated();

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('productos', 'public');
            $datos['imagen_url'] = $path;
            unset($datos['imagen']); // Quitamos el archivo binario del array
        }

        $producto = Producto::create($datos);
        
        return response()->json([
            'message' => 'Producto creado exitosamente',
            'data' => $producto
        ], 201);
    }

    /**
     * Muestra el producto especificado.
     */
    public function show(Producto $producto)
    {
        // Cargamos la relación 'categoria' para este producto específico
        $producto->load('categoria');
        
        return response()->json($producto, 200);
    }

    /**
     * Actualiza el producto especificado.
     */
    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        $datos = $request->validated();

        if ($request->hasFile('imagen')) {
            // Borrar imagen anterior si existe
            if ($producto->imagen_url && Storage::disk('public')->exists($producto->imagen_url)) {
                Storage::disk('public')->delete($producto->imagen_url);
            }
            $path = $request->file('imagen')->store('productos', 'public');
            $datos['imagen_url'] = $path;
            unset($datos['imagen']);
        }

        $producto->update($datos);

        return response()->json([
            'message' => 'Producto actualizado exitosamente',
            'data' => $producto
        ], 200);
    }

    /**
     * Elimina el producto especificado.
     */
    public function destroy(Producto $producto)
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        if ($producto->imagen_url && Storage::disk('public')->exists($producto->imagen_url)) {
            Storage::disk('public')->delete($producto->imagen_url);
        }

        $producto->delete();

        return response()->json([
            'message' => 'Producto eliminado exitosamente'
        ], 200);
    }
}