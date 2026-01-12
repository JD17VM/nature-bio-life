<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    /**
     * Listar todas las notificaciones del usuario autenticado.
     */
    public function index(Request $request)
    {
        // RF-100: Centro de notificaciones
        $user = $request->user();

        // Obtenemos todas, paginadas
        // Laravel usa internamente el modelo DatabaseNotification aquí
        $notificaciones = $user->notifications()->paginate(15);

        return response()->json([
            'message' => 'Notificaciones obtenidas correctamente',
            'data' => $notificaciones,
            'unread_count' => $user->unreadNotifications->count()
        ]);
    }

    /**
     * Listar solo las no leídas.
     */
    public function unread(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'message' => 'Notificaciones no leídas',
            'data' => $user->unreadNotifications
        ]);
    }

    /**
     * Marcar una notificación específica como leída.
     */
    public function markAsRead(Request $request, $id)
    {
        // RF-101: Marcar como leída
        $user = $request->user();
        
        // Busca la notificación dentro de las del usuario
        $notification = $user->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Notificación marcada como leída']);
        }

        return response()->json(['message' => 'Notificación no encontrada'], 404);
    }

    /**
     * Marcar TODAS como leídas.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'Todas las notificaciones marcadas como leídas']);
    }
}