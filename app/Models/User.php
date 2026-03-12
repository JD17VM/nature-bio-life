<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // --- DEFINICIÓN DE ROLES ---
    const ROL_ADMIN = 'admin';
    const ROL_SOCIO = 'socio';
    const ROL_VENDEDOR = 'vendedor';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre_completo',
        'email',
        'password',
        'telefono',
        'dni',
        'codigo_referido',
        'direccion',
        'info_bancaria_encriptada',
        'activo',
        'patrocinador_id',
        'rol',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'info_bancaria_encriptada', // Ocultamos info sensible
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // Casts para los campos nuevos
            'activo' => 'boolean',
            'bloqueado_hasta' => 'datetime',
        ];
    }

    // --- RELACIONES DE PATROCINIO ---

    /**
     * Obtiene el usuario que es patrocinador de este usuario.
     */
    public function patrocinador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patrocinador_id');
    }

    /**
     * Obtiene la lista de usuarios que este usuario ha patrocinado (referidos directos).
     */
    public function referidos(): HasMany
    {
        return $this->hasMany(User::class, 'patrocinador_id');
    }

    public function videosVistos()
    {
        return $this->belongsToMany(Video::class, 'video_user')
                    ->withPivot(['segundo_actual', 'completado', 'fecha_completado'])
                    ->withTimestamps();
    }

    /**
     * Verifica si el usuario tiene rol de administrador.
     */
    public function isAdmin(): bool
    {
        return $this->rol === self::ROL_ADMIN;
    }

    /**
     * Verifica si el usuario es Socio
     */
    public function isSocio(): bool
    {
        return $this->rol === self::ROL_SOCIO;
    }

    /**
     * Verifica si el usuario es Vendedor
     */
    public function isVendedor(): bool
    {
        return $this->rol === self::ROL_VENDEDOR;
    }
}