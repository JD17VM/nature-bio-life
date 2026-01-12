<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comision extends Model
{
    // Forzamos el nombre de la tabla por si Laravel intenta usar "comisions"
    protected $table = 'comisiones';

    protected $fillable = [
        'vendedor_id', 'comprador_id', 'pedido_id',
        'monto_compra', 'porcentaje', 'monto_comision',
        'estado', 'fecha_generacion', 'fecha_pago', 'observaciones'
    ];

    protected $casts = [
        'fecha_generacion' => 'datetime',
        'fecha_pago' => 'datetime',
        'monto_compra' => 'decimal:2',
        'porcentaje' => 'decimal:2',
        'monto_comision' => 'decimal:2',
    ];

    // Relación: El usuario que GANA la comisión
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    // Relación: El usuario que COMPRÓ
    public function comprador()
    {
        return $this->belongsTo(User::class, 'comprador_id');
    }

    // Relación: El pedido origen
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}