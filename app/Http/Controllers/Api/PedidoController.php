<?php

namespace App\Http\Controllers\Api;

use App\Enums\EstadoComisionEnum;
use App\Enums\EstadoPedidoEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Pedido\StorePedidoRequest;
use App\Http\Requests\Pedido\UpdateEstadoPedidoRequest;
use App\Models\Comision;
use App\Models\Configuracion;
use App\Models\EstadoPedido;
use App\Models\HistorialPuntos;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Producto;
use App\Traits\HandlesBase64Files;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PedidoController extends Controller
{
    use HandlesBase64Files;
    /**
     * Muestra la lista de pedidos del usuario autenticado.
     * GET /api/pedidos
     */
    public function index(Request $request)
    {
        $user    = $request->user();
        $perPage = min((int) $request->query('per_page', 15), 100);

        $query = Pedido::with('detalles.producto')->orderBy('created_at', 'desc');

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return response()->json($query->paginate($perPage));
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

        // Manejo del archivo de comprobante en Base64
        $pathComprobante = null;
        if ($request->filled('comprobante')) {
            $pathComprobante = $this->saveBase64File($request->input('comprobante'), 'comprobantes');
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
     * Actualiza el estado de un pedido (solo Admin).
     * PATCH /api/pedidos/{id}/estado
     */
    public function actualizarEstado(UpdateEstadoPedidoRequest $request, $id)
    {
        $pedido = Pedido::with('detalles.producto')->findOrFail($id);

        $estadoActual = $pedido->estado;                                    // EstadoPedidoEnum
        $nuevoEstado  = EstadoPedidoEnum::from($request->input('estado'));  // EstadoPedidoEnum

        // Validar que la transición sea permitida
        if (!$estadoActual->puedeCambiarA($nuevoEstado)) {
            return response()->json([
                'message' => "No se puede cambiar el estado de \"{$estadoActual->label()}\" a \"{$nuevoEstado->label()}\".",
                'transiciones_permitidas' => array_map(
                    fn($e) => ['valor' => $e->value, 'label' => $e->label()],
                    $estadoActual->transicionesPermitidas()
                ),
            ], 422);
        }

        DB::transaction(function () use ($pedido, $nuevoEstado, $request) {
            // 1. Actualizar estado en el pedido
            $pedido->update(['estado' => $nuevoEstado]);

            // 2. Registrar en el historial de estados
            EstadoPedido::create([
                'pedido_id'     => $pedido->id,
                'estado'        => $nuevoEstado,
                'observaciones' => $request->input('observaciones'),
            ]);

            // 3. Lógica de negocio según el nuevo estado
            if ($nuevoEstado === EstadoPedidoEnum::ENTREGADO) {
                $this->acreditarPuntos($pedido);
                $this->generarComisionReferido($pedido);
            }

            if ($nuevoEstado === EstadoPedidoEnum::CANCELADO) {
                $this->anularComisionesPendientes($pedido);
            }
        });

        return response()->json([
            'message' => "Estado del pedido actualizado a \"{$nuevoEstado->label()}\" exitosamente.",
            'data'    => $pedido->fresh(['detalles.producto', 'estados']),
        ], 200);
    }

    /**
     * Acredita los puntos del pedido al saldo del comprador y registra el movimiento.
     * Solo se ejecuta si el pedido aún no tiene puntos acreditados.
     */
    private function acreditarPuntos(Pedido $pedido): void
    {
        $puntosGanados = (int) $pedido->puntos_ganados;

        if ($puntosGanados <= 0) {
            return;
        }

        // Evitar doble acreditación si ya existe un registro de ingreso para este pedido
        $yaAcreditado = HistorialPuntos::where('pedido_id', $pedido->id)
            ->where('tipo', 'ingreso')
            ->exists();

        if ($yaAcreditado) {
            return;
        }

        $comprador      = $pedido->usuario()->lockForUpdate()->first();
        $balanceAnterior = $comprador->puntos_saldo;
        $balanceNuevo    = $balanceAnterior + $puntosGanados;

        // Actualizar saldo del usuario
        $comprador->update(['puntos_saldo' => $balanceNuevo]);

        // Registrar movimiento en historial
        HistorialPuntos::create([
            'user_id'         => $comprador->id,
            'pedido_id'       => $pedido->id,
            'puntos'          => $puntosGanados,
            'tipo'            => 'ingreso',
            'balance_anterior' => $balanceAnterior,
            'balance_nuevo'    => $balanceNuevo,
            'descripcion'     => "Puntos por pedido #{$pedido->numero_pedido}",
        ]);
    }

    /**
     * Genera la comisión para el patrocinador cuando un pedido es entregado.
     * Solo se genera si el comprador tiene patrocinador y aún no existe comisión para este pedido.
     */
    private function generarComisionReferido(Pedido $pedido): void
    {
        // Cargar el usuario comprador con su patrocinador
        $comprador = $pedido->usuario()->with('patrocinador')->first();

        // Si el comprador no tiene patrocinador, no hay comisión que generar
        if (!$comprador?->patrocinador_id) {
            return;
        }

        // Evitar duplicados: si ya existe comisión para este pedido, no crear otra
        $yaExiste = Comision::where('pedido_id', $pedido->id)->exists();
        if ($yaExiste) {
            return;
        }

        // Obtener el porcentaje de comisión desde configuraciones (default 10%)
        $config      = Configuracion::where('clave', 'porcentaje_comision')->first();
        $porcentaje  = $config ? (float) $config->valor : 10.0;
        $montoCompra = (float) $pedido->total;

        Comision::create([
            'vendedor_id'    => $comprador->patrocinador_id,
            'comprador_id'   => $comprador->id,
            'pedido_id'      => $pedido->id,
            'monto_compra'   => $montoCompra,
            'porcentaje'     => $porcentaje,
            'monto_comision' => round($montoCompra * $porcentaje / 100, 2),
            'estado'         => EstadoComisionEnum::PENDIENTE,
        ]);
    }

    /**
     * Anula todas las comisiones pendientes asociadas a un pedido cancelado.
     */
    private function anularComisionesPendientes(Pedido $pedido): void
    {
        Comision::where('pedido_id', $pedido->id)
            ->where('estado', EstadoComisionEnum::PENDIENTE)
            ->update(['estado' => EstadoComisionEnum::ANULADA]);
    }

    /**
     * Sube el comprobante de pago para un pedido existente.
     */
    public function confirmarPago(Request $request, $id)
    {
        $request->validate([
            'comprobante' => 'required|string', // Imagen en Base64
            'codigo_transaccion' => 'nullable|string|max:255',
            'notas' => 'nullable|string'
        ]);

        // Buscar el pedido y asegurar que pertenezca al usuario autenticado
        $pedido = Pedido::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();

        $path = $this->saveBase64File($request->input('comprobante'), 'comprobantes');

        if (!$path) {
            return response()->json(['message' => 'El formato del comprobante no es válido.'], 422);
        }

        $pedido->update([
            'comprobante_pago' => $path,
            'codigo_transaccion' => $request->codigo_transaccion,
            'notas' => $request->notas ? $request->notas : $pedido->notas,
        ]);

        return response()->json(['message' => 'Comprobante subido exitosamente. Tu pedido está en verificación.']);
    }
}