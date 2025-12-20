<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialPuntos extends Model
{
    protected $table = 'historial_puntos';

    protected $fillable = [
        'user_id', 'pedido_id', 'puntos', 'tipo',
        'balance_anterior', 'balance_nuevo', 'fecha', 'descripcion'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}