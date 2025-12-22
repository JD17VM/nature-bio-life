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

    protected $appends = ['visto'];

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

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'video_user')
                    ->withPivot(['segundo_actual', 'completado', 'fecha_completado'])
                    ->withTimestamps();
    }

    public function getVistoAttribute()
    {
        // Si no hay usuario autenticado (ej: acceso público), retorna false
        if (!auth('sanctum')->check()) {
            return false;
        }

        // Verifica en la tabla pivote si este usuario ya lo marcó como completado
        return $this->usuarios()
                    ->where('user_id', auth('sanctum')->id())
                    ->wherePivot('completado', true)
                    ->exists();
    }
}