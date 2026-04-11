<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\Producto\StoreProductoRequest;
use App\Http\Requests\Producto\UpdateProductoRequest;
use App\Traits\HandlesBase64Files;

class ProductoController extends Controller
{
    use HandlesBase64Files;
    /**
     * Muestra una lista de los productos activos.
     */
    public function index(Request $request)
    {
        $perPage = min((int) $request->query('per_page', 15), 100);

        $productos = Producto::with('categoria')
            ->where('activo', true)
            ->paginate($perPage);

        return response()->json($productos, 200);
    }

    /**
     * Almacena un nuevo producto.
     */
    public function store(StoreProductoRequest $request)
    {
        // Permitimos acceso solo a Admin
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado. Solo Admin puede crear productos.'], 403);
        }

        $datos = $request->validated();

        if ($request->filled('imagen')) {
            $path = $this->saveBase64File($request->input('imagen'), 'productos');
            if ($path) {
                $datos['imagen_url'] = $path;
            }
            unset($datos['imagen']);
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
        // Permitimos acceso solo a Admin
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $datos = $request->validated();

        if ($request->filled('imagen')) {
            // Borrar imagen anterior si existe
            if ($producto->imagen_url && Storage::disk('public')->exists($producto->imagen_url)) {
                Storage::disk('public')->delete($producto->imagen_url);
            }
            
            $path = $this->saveBase64File($request->input('imagen'), 'productos');
            if ($path) {
                $datos['imagen_url'] = $path;
            }
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
        // AQUÍ LA DIFERENCIA: Solo el Admin puede eliminar. El Almacenero NO.
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