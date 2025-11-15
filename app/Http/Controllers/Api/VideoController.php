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
        $video->delete();

        return response()->json([
            'message' => 'Video eliminado exitosamente'
        ], 200);
    }
}