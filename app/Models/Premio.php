<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Premio extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'puntos_requeridos',
        'stock',
        'categoria_premio_id',
        'imagen_url',
        'disponible',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'puntos_requeridos' => 'integer',
        'stock' => 'integer',
        'categoria_premio_id' => 'integer',
        'disponible' => 'boolean',
    ];

    /**
     * Obtiene la categoría a la que pertenece el premio.
     */
    public function categoriaPremio(): BelongsTo
    {
        return $this->belongsTo(CategoriaPremio::class, 'categoria_premio_id');
    }
}