<?php

namespace App\Notifications\Comisiones;

use Illuminate\Notifications\Notification;
use App\Models\Comision;

class ComisionGenerada extends Notification
{
    protected $comision;

    public function __construct(Comision $comision)
    {
        $this->comision = $comision;
    }

    /**
     * Especificar el canal de notificación
     * 'database' = guardar en tabla notifications
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Datos a guardar en la tabla notifications
     * Retorna solo titulo y mensaje para respuesta estandarizada
     */
    public function toArray($notifiable)
    {
        // Obtener información del producto y cliente
        $producto_nombre = $this->comision->pedido->detallesPedidos->first()?->producto->nombre ?? 'Producto';
        $cliente_nombre = $this->comision->comprador->nombre_completo ?? 'Cliente';

        return [
            'titulo' => '¡Comisión Ganada!',
            'mensaje' => "Generaste una comisión de \${$this->comision->monto} ({$this->comision->porcentaje}%) por la venta de {$producto_nombre} a {$cliente_nombre}.",
        ];
    }
}
