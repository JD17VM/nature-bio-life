<?php

namespace App\Models;

use App\Enums\EstadoPedidoEnum;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'numero_pedido', 'user_id', 'subtotal', 'costo_envio',
        'total', 'puntos_ganados', 'estado', 'notas',
        'fecha_pedido', 'fecha_actualizacion',
        'comprobante_pago', 'codigo_transaccion'
    ];

    protected $casts = [
        'estado' => EstadoPedidoEnum::class,
    ];

    // Relación: Un pedido pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación: Un pedido tiene muchos productos (detalles)
    public function detalles()
    {
        return $this->hasMany(DetallePedido::class);
    }

    // Relación: Un pedido tiene un historial de estados
    public function estados()
    {
        return $this->hasMany(EstadoPedido::class);
    }
}