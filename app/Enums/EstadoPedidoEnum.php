<?php

namespace App\Enums;

enum EstadoPedidoEnum: string
{
    // Cliente creó el pedido, aún no sube comprobante
    case PENDIENTE = 'pendiente';

    // Cliente subió el comprobante, admin lo está verificando
    case VERIFICANDO_PAGO = 'verificando_pago';

    // Pago confirmado, se está preparando el despacho
    case PROCESANDO = 'procesando';

    // Paquete enviado, en camino al cliente
    case ENVIADO = 'enviado';

    // Cliente recibió el pedido (estado final positivo)
    case ENTREGADO = 'entregado';

    // Pedido cancelado (estado final negativo)
    case CANCELADO = 'cancelado';

    /**
     * Etiqueta legible en español para mostrar al frontend.
     */
    public function label(): string
    {
        return match($this) {
            self::PENDIENTE        => 'Pendiente',
            self::VERIFICANDO_PAGO => 'Verificando Pago',
            self::PROCESANDO       => 'Procesando',
            self::ENVIADO          => 'Enviado',
            self::ENTREGADO        => 'Entregado',
            self::CANCELADO        => 'Cancelado',
        };
    }

    /**
     * Estados a los que se puede transicionar desde el estado actual.
     * Evita cambios de estado inválidos (ej: de Cancelado a Enviado).
     *
     * @return EstadoPedidoEnum[]
     */
    public function transicionesPermitidas(): array
    {
        return match($this) {
            self::PENDIENTE        => [self::VERIFICANDO_PAGO, self::CANCELADO],
            self::VERIFICANDO_PAGO => [self::PROCESANDO, self::CANCELADO],
            self::PROCESANDO       => [self::ENVIADO, self::CANCELADO],
            self::ENVIADO          => [self::ENTREGADO, self::CANCELADO],
            self::ENTREGADO        => [], // Estado final: no permite más cambios
            self::CANCELADO        => [], // Estado final: no permite más cambios
        };
    }

    /**
     * Verifica si se puede cambiar al estado dado desde el estado actual.
     */
    public function puedeCambiarA(self $nuevoEstado): bool
    {
        return in_array($nuevoEstado, $this->transicionesPermitidas());
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
