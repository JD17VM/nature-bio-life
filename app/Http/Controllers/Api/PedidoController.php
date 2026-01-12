<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Producto;
use App\Http\Requests\Pedido\StorePedidoRequest;
use App\Http\Requests\Pedido\UpdatePedidoRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PedidoController extends Controller
{
    public function index()
    {
        // Trae pedidos con sus relaciones
        $pedidos = Pedido::with(['usuario:id,nombre_completo', 'detalles.producto'])
            ->latest()
            ->get();
        return response()->json($pedidos);
    }

    public function store(StorePedidoRequest $request)
    {
        try {
            // DB::transaction asegura que todo se guarde o nada se guarde
            return DB::transaction(function () use ($request) {
                $data = $request->validated();
                
                $subtotal = 0;
                $puntosTotales = 0;
                $detallesListos = [];

                // 1. Procesar cada producto para calcular totales y verificar stock
                foreach ($data['detalles'] as $item) {
                    $producto = Producto::lockForUpdate()->find($item['producto_id']);
                    
                    if ($producto->stock < $item['cantidad']) {
                        throw new \Exception("Stock insuficiente para: " . $producto->nombre);
                    }

                    $lineaSubtotal = $producto->precio * $item['cantidad'];
                    $lineaPuntos = $producto->puntos * $item['cantidad'];

                    $subtotal += $lineaSubtotal;
                    $puntosTotales += $lineaPuntos;

                    $detallesListos[] = [
                        'producto_id' => $producto->id,
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $producto->precio,
                        'subtotal' => $lineaSubtotal,
                        'puntos_unitarios' => $producto->puntos
                    ];

                    // Restar del stock
                    $producto->decrement('stock', $item['cantidad']);
                }

                $costoEnvio = $data['costo_envio'] ?? 0;
                $total = $subtotal + $costoEnvio;

                // 2. Crear la cabecera del Pedido
                $pedido = Pedido::create([
                    'numero_pedido' => 'PED-' . strtoupper(Str::random(8)),
                    'user_id' => $data['user_id'],
                    'subtotal' => $subtotal,
                    'costo_envio' => $costoEnvio,
                    'total' => $total,
                    'puntos_ganados' => $puntosTotales,
                    'estado' => 'pendiente',
                    'notas' => $data['notas'] ?? null
                ]);

                // 3. Guardar todos los detalles de golpe
                $pedido->detalles()->createMany($detallesListos);

                // 4. Crear estado inicial en el historial
                $pedido->estados()->create([
                    'estado' => 'pendiente',
                    'observaciones' => 'Pedido creado exitosamente'
                ]);

                return response()->json([
                    'message' => 'Pedido creado correctamente', 
                    'data' => $pedido->load('detalles')
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function show(Pedido $pedido)
    {
        return response()->json($pedido->load(['usuario', 'detalles.producto', 'estados']));
    }

    public function update(UpdatePedidoRequest $request, Pedido $pedido)
    {
        $pedido->update($request->validated());

        // Si cambió el estado, lo registramos en el historial
        if ($request->has('estado')) {
            $pedido->estados()->create([
                'estado' => $request->estado,
                'observaciones' => 'Cambio de estado manual'
            ]);
        }

        return response()->json(['message' => 'Pedido actualizado', 'data' => $pedido]);
    }

    public function destroy(Pedido $pedido)
    {
        $pedido->delete();
        return response()->json(['message' => 'Pedido eliminado']);
    }
}