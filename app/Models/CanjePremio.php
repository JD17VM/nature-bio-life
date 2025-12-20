<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CanjePremio extends Model
{
    protected $table = 'canje_premios';

    protected $fillable = [
        'user_id', 'premio_id', 'puntos_utilizados', 'estado',
        'fecha_canje', 'fecha_aprobacion', 'fecha_entrega', 'observaciones'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function premio()
    {
        return $this->belongsTo(Premio::class); // Asegúrate de tener el modelo Premio
    }
}