<?php

namespace App\Enums;

enum RolUsuarioEnum: string
{
    case ADMIN    = 'admin';
    case SOCIO    = 'socio';
    case VENDEDOR = 'vendedor';

    /**
     * Etiqueta legible en español para mostrar al frontend.
     */
    public function label(): string
    {
        return match($this) {
            self::ADMIN    => 'Administrador',
            self::SOCIO    => 'Socio',
            self::VENDEDOR => 'Vendedor',
        };
    }

    /**
     * Devuelve todos los valores como array (útil para validaciones).
     *
     * @return string[]
     */
    public static function valores(): array
    {
        return array_column(self::cases(), 'value');
    }
}
