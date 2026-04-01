<?php

namespace App\Http\Controllers\Api;

use App\Enums\EstadoPedidoEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Pedido\StorePedidoRequest;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PedidoController extends Controller
{
    /**
     * Muestra la lista de pedidos del usuario autenticado.
     * GET /api/pedidos
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Obtenemos los pedidos ordenados por fecha (más reciente primero)
        // Usamos 'with' para traer también los detalles y productos de una vez
        $query = Pedido::with('detalles.producto')->orderBy('created_at', 'desc');

        // Si NO es administrador, filtramos para que solo vea SUS pedidos
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return response()->json($query->get());
    }

    /**
     * Muestra el detalle de un pedido específico.
     * GET /api/pedidos/{id}
     */
    public function show($id)
    {
        $pedido = Pedido::with('detalles.producto')->findOrFail($id);

        // Seguridad: Verificar que el pedido pertenezca al usuario (o que sea admin)
        if (!auth()->user()->isAdmin() && $pedido->user_id !== auth()->id()) {
            return response()->json(['message' => 'No tienes permiso para ver este pedido.'], 403);
        }

        return response()->json($pedido);
    }

    /**
     * Crea un nuevo pedido a partir de una lista de productos.
     */
    public function store(StorePedidoRequest $request)
    {
        $user = $request->user();
        $inputDetalles = $request->validated()['detalles'];
        $datosExtra = $request->only(['codigo_transaccion', 'notas']);

        // Manejo del archivo de comprobante (si se envía)
        $pathComprobante = null;
        if ($request->hasFile('comprobante')) {
            // Guarda en storage/app/public/comprobantes
            $pathComprobante = $request->file('comprobante')->store('comprobantes', 'public');
        }

        try {
            // Usamos una transacción para asegurar que se guarde todo o nada
            $resultado = DB::transaction(function () use ($user, $inputDetalles, $datosExtra, $pathComprobante) {
                
                $totalPedido = 0;
                $totalPuntos = 0;
                $detallesParaGuardar = [];

                // 1. Validar Stock y Calcular Totales (Pre-procesamiento)
                foreach ($inputDetalles as $item) {
                    // Bloqueamos el registro para evitar condiciones de carrera en el stock
                    $producto = Producto::lockForUpdate()->find($item['producto_id']);

                    if ($producto->stock < $item['cantidad']) {
                        throw new \Exception("No hay suficiente stock para el producto: {$producto->nombre}");
                    }

                    $subtotal = $producto->precio * $item['cantidad'];
                    $puntos = $producto->puntos * $item['cantidad'];

                    $totalPedido += $subtotal;
                    $totalPuntos += $puntos;

                    $detallesParaGuardar[] = [
                        'producto' => $producto,
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $producto->precio,
                        'subtotal' => $subtotal,
                        'puntos_unitarios' => $producto->puntos,
                    ];
                }

                // 2. Crear el Pedido (Cabecera)
                $pedido = Pedido::create([
                    'numero_pedido' => 'ORD-' . strtoupper(Str::random(10)), // Genera un código único
                    'user_id' => $user->id,
                    'subtotal' => $totalPedido, // Agregamos el campo faltante
                    'total' => $totalPedido,
                    'puntos_ganados' => $totalPuntos,
                    'estado' => EstadoPedidoEnum::PENDIENTE,
                    'comprobante_pago' => $pathComprobante,
                    'codigo_transaccion' => $datosExtra['codigo_transaccion'] ?? null,
                    'notas' => $datosExtra['notas'] ?? null,
                ]);

                // 3. Guardar Detalles y Actualizar Stock
                foreach ($detallesParaGuardar as $detalle) {
                    DetallePedido::create([
                        'pedido_id' => $pedido->id,
                        'producto_id' => $detalle['producto']->id,
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $detalle['precio_unitario'],
                        'subtotal' => $detalle['subtotal'],
                        'puntos_unitarios' => $detalle['puntos_unitarios'],
                    ]);

                    // Descontar stock
                    $detalle['producto']->decrement('stock', $detalle['cantidad']);
                }

                return $pedido;
            });

            return response()->json(['message' => 'Pedido creado exitosamente', 'data' => $resultado], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al procesar el pedido: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Sube el comprobante de pago para un pedido existente.
     */
    public function confirmarPago(Request $request, $id)
    {
        $request->validate([
            'comprobante' => 'required|image|max:5120', // Máx 5MB
            'codigo_transaccion' => 'nullable|string|max:255',
            'notas' => 'nullable|string'
        ]);

        // Buscar el pedido y asegurar que pertenezca al usuario autenticado
        $pedido = Pedido::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();

        $path = $request->file('comprobante')->store('comprobantes', 'public');

        $pedido->update([
            'comprobante_pago' => $path,
            'codigo_transaccion' => $request->codigo_transaccion,
            'notas' => $request->notas ? $request->notas : $pedido->notas,
        ]);

        return response()->json(['message' => 'Comprobante subido exitosamente. Tu pedido está en verificación.']);
    }
}