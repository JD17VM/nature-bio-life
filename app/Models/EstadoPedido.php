<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoPedido extends Model
{
    protected $fillable = ['pedido_id', 'estado', 'observaciones', 'fecha_cambio'];
}