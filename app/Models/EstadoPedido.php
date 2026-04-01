<?php

namespace App\Models;

use App\Enums\EstadoPedidoEnum;
use Illuminate\Database\Eloquent\Model;

class EstadoPedido extends Model
{
    protected $fillable = ['pedido_id', 'estado', 'observaciones', 'fecha_cambio'];

    protected $casts = [
        'estado'      => EstadoPedidoEnum::class,
        'fecha_cambio' => 'datetime',
    ];
}