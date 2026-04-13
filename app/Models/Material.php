<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $table = 'materiales';
    protected $fillable = [
        'titulo',
        'descripcion',
        'archivo_url',
        'tipo_material_id',
        'activo',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tipo_material_id' => 'integer',
        'activo' => 'boolean',
    ];

    /**
     * Obtiene el tipo al que pertenece el material.
     */
    public function tipoMaterial(): BelongsTo
    {
        return $this->belongsTo(TipoMaterial::class);
    }
}