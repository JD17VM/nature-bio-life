<?php

namespace App\Enums;

enum EstadoComisionEnum: string
{
    // Comisión generada automáticamente, pendiente de revisión del admin
    case PENDIENTE = 'pendiente';

    // Admin aprobó la comisión, lista para pagar
    case APROBADA = 'aprobada';

    // Comisión abonada al vendedor
    case PAGADA = 'pagada';

    // Comisión anulada (ej: pedido cancelado o devuelto)
    case ANULADA = 'anulada';

    /**
     * Etiqueta legible en español para mostrar al frontend.
     */
    public function label(): string
    {
        return match($this) {
            self::PENDIENTE => 'Pendiente',
            self::APROBADA  => 'Aprobada',
            self::PAGADA    => 'Pagada',
            self::ANULADA   => 'Anulada',
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
