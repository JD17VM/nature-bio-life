<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

use App\Http\Requests\Video\StoreVideoRequest;
use App\Http\Requests\Video\UpdateVideoRequest;

class VideoController extends Controller
{
    /**
     * Muestra una lista de los videos activos.
     */
    public function index()
    {
        $videos = Video::with('categoriaVideo')
                       ->where('activo', true)
                       ->orderBy('created_at', 'desc')
                       ->get();

        if ($videos->isEmpty()) {
            return response()->json([
                'message' => 'No hay videos registrados',
                'status' => 404
            ], 404);
        }

        return response()->json($videos, 200);
    }

    /**
     * Almacena un nuevo video.
     */
    public function store(StoreVideoRequest $request)
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $video = Video::create($request->validated());
        
        return response()->json([
            'message' => 'Video creado exitosamente',
            'data' => $video
        ], 201);
    }

    /**
     * Muestra el video especificado.
     */
    public function show(Video $video)
    {
        $video->load('categoriaVideo');
        return response()->json($video, 200);
    }

    /**
     * Actualiza el video especificado.
     */
    public function update(UpdateVideoRequest $request, Video $video)
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $video->update($request->validated());

        return response()->json([
            'message' => 'Video actualizado exitosamente',
            'data' => $video
        ], 200);
    }

    /**
     * Elimina el video especificado.
     */
    public function destroy(Video $video)
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $video->delete();

        return response()->json([
            'message' => 'Video eliminado exitosamente'
        ], 200);
    }

    public function guardarProgreso(Request $request, $id)
    {
        $request->validate([
            'segundo_actual' => 'required|integer|min:0',
            'completado' => 'boolean'
        ]);

        $video = Video::findOrFail($id);
        $user = $request->user();

        // syncWithoutDetaching: Si ya existe el registro, lo actualiza; si no, lo crea.
        // No borra otros videos vistos.
        $user->videosVistos()->syncWithoutDetaching([
            $video->id => [
                'segundo_actual' => $request->segundo_actual,
                'completado' => $request->completado ?? false,
                'fecha_completado' => ($request->completado ?? false) ? now() : null
            ]
        ]);

        return response()->json(['message' => 'Progreso guardado correctamente']);
    }

    /**
     * Muestra el porcentaje de avance del usuario en la capacitación.
     * RF-090: Mostrar progreso (ej: 15 de 50 videos).
     */
    public function estadisticas(Request $request)
    {
        $user = $request->user();
        
        // 1. Total de videos disponibles y activos en el sistema
        $totalVideos = Video::where('activo', true)->count();
        
        // 2. Total de videos que el usuario ha marcado como 'completado'
        $videosCompletados = $user->videosVistos()->wherePivot('completado', true)->count();
        
        // 3. Cálculo del porcentaje
        $porcentaje = $totalVideos > 0 ? round(($videosCompletados / $totalVideos) * 100) : 0;

        return response()->json([
            'total_videos_disponibles' => $totalVideos,
            'videos_completados' => $videosCompletados,
            'porcentaje_avance' => $porcentaje . '%'
        ]);
    }
}