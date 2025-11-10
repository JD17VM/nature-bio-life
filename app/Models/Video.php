<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'titulo',
        'descripcion',
        'url',
        'thumbnail_url',
        'duracion_segundos',
        'categoria_video_id',
        'nivel',
        'activo',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'duracion_segundos' => 'integer',
        'categoria_video_id' => 'integer',
        'activo' => 'boolean',
    ];

    /**
     * Obtiene la categoría a la que pertenece el video.
     */
    public function categoriaVideo(): BelongsTo
    {
        return $this->belongsTo(CategoriaVideo::class, 'categoria_video_id');
    }
}