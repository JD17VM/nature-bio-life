<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
        $producto = Producto::create($request->all());
        
        return response()->json([
            'message' => 'Producto creado exitosamente',
            'data' => $producto
        ], 201);
    }

    /**
     * Muestra el producto especificado.
     */
    public function show($id)
    {
        $producto = Producto::with('categoria')->find($id);

        if (!$producto) {
            return response()->json([
                'message' => 'Producto no encontrado',
                'status' => 404
            ], 404);
        }

        return response()->json($producto, 200);
    }

    /**
     * Actualiza el producto especificado.
     */
    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'message' => 'Producto no encontrado',
                'status' => 404
            ], 404);
        }

        $producto->update($request->all());

        return response()->json([
            'message' => 'Producto actualizado exitosamente',
            'data' => $producto
        ], 200);
    }

    /**
     * Elimina el producto especificado.
     */
    public function destroy($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'message' => 'Producto no encontrado',
                'status' => 404
            ], 404);
        }

        $producto->delete();

        return response()->json([
            'message' => 'Producto eliminado exitosamente'
        ], 200);
    }
}