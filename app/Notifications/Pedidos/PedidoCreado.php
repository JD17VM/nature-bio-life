<?php

namespace App\Notifications\Pedidos;

use Illuminate\Notifications\Notification;
use App\Models\Pedido;

class PedidoCreado extends Notification
{
    protected $pedido;

    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
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
        return [
            'titulo' => 'Pedido Creado',
            'mensaje' => "Tu pedido {$this->pedido->numero_pedido} por \${$this->pedido->total} ha sido recibido. Ganarás {$this->pedido->puntos_ganados} puntos cuando se complete.",
        ];
    }
}
