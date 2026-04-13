<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     * Valida que el usuario autenticado tenga rol ADMIN.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtener usuario autenticado
        $user = $request->user();

        // Validar que existe usuario y es admin
        if (!$user || !$user->isAdmin()) {
            return response()->json(
                ['message' => 'No autorizado. Se requiere rol de ADMIN.'],
                403
            );
        }

        return $next($request);
    }
}
