<?php

namespace App\Notifications\Canjes;

use Illuminate\Notifications\Notification;
use App\Models\CanjePremio;

class CanjeAprobado extends Notification
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
            'titulo' => '¡Canje Aprobado!',
            'mensaje' => "Tu canje por '{$this->canje->premio->nombre}' fue aprobado. Se enviará en 3-5 días hábiles.",
        ];
    }
}
