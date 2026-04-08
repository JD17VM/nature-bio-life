<?php

namespace App\Enums;

enum EstadoCanjeEnum: string
{
    // Solicitud de canje recibida, pendiente de revisión
    case PENDIENTE = 'pendiente';

    // Admin aprobó el canje, preparando entrega
    case APROBADO = 'aprobado';

    // Premio entregado al usuario (estado final positivo)
    case ENTREGADO = 'entregado';

    // Solicitud rechazada por admin (estado final negativo)
    case RECHAZADO = 'rechazado';

    /**
     * Etiqueta legible en español para mostrar al frontend.
     */
    public function label(): string
    {
        return match($this) {
            self::PENDIENTE  => 'Pendiente',
            self::APROBADO   => 'Aprobado',
            self::ENTREGADO  => 'Entregado',
            self::RECHAZADO  => 'Rechazado',
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
