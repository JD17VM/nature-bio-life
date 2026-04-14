<?php

namespace App\Notifications\Canjes;

use Illuminate\Notifications\Notification;
use App\Models\CanjePremio;

class CanjeCreado extends Notification
{
    protected $canje;

    public function __construct(CanjePremio $canje)
    {
        $this->canje = $canje;
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
            'titulo' => 'Canje Solicitado',
            'mensaje' => "Tu canje por '{$this->canje->premio->nombre}' ({$this->canje->puntos_utilizados} puntos) ha sido registrado. Espera la aprobación de nuestro equipo.",
        ];
    }
}
