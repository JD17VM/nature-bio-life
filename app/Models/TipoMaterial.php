<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoMaterial extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'extension_permitida',
    ];

    protected $table = 'tipo_materiales'; 

    /**
     * Obtiene los materiales asociados con este tipo.
     */
    public function materiales(): HasMany
    {
        return $this->hasMany(Material::class);
    }
}