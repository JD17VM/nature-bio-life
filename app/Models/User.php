<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Enums\RolUsuarioEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
        'puntos_saldo',
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
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'activo'            => 'boolean',
        'puntos_saldo'      => 'integer',
        'bloqueado_hasta'   => 'datetime',
        'rol'               => RolUsuarioEnum::class,
    ];

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

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'user_id');
    }

    public function videosVistos()
    {
        return $this->belongsToMany(Video::class, 'video_user')
                    ->withPivot(['segundo_actual', 'completado', 'fecha_completado'])
                    ->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->rol === RolUsuarioEnum::ADMIN
            || $this->getRawOriginal('rol') === RolUsuarioEnum::ADMIN->value;
    }

    public function isSocio(): bool
    {
        return $this->rol === RolUsuarioEnum::SOCIO
            || $this->getRawOriginal('rol') === RolUsuarioEnum::SOCIO->value;
    }

    public function isVendedor(): bool
    {
        return $this->rol === RolUsuarioEnum::VENDEDOR
            || $this->getRawOriginal('rol') === RolUsuarioEnum::VENDEDOR->value;
    }
}