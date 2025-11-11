<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
        $video = Video::create($request->all());
        
        return response()->json([
            'message' => 'Video creado exitosamente',
            'data' => $video
        ], 201);
    }

    /**
     * Muestra el video especificado.
     */
    public function show($id)
    {
        $video = Video::with('categoriaVideo')->find($id);

        if (!$video) {
            return response()->json([
                'message' => 'Video no encontrado',
                'status' => 404
            ], 404);
        }

        return response()->json($video, 200);
    }

    /**
     * Actualiza el video especificado.
     */
    public function update(Request $request, $id)
    {
        $video = Video::find($id);

        if (!$video) {
            return response()->json([
                'message' => 'Video no encontrado',
                'status' => 404
            ], 404);
        }

        $video->update($request->all());

        return response()->json([
            'message' => 'Video actualizado exitosamente',
            'data' => $video
        ], 200);
    }

    /**
     * Elimina el video especificado.
     */
    public function destroy($id)
    {
        $video = Video::find($id);

        if (!$video) {
            return response()->json([
                'message' => 'Video no encontrado',
                'status' => 404
            ], 404);
        }

        $video->delete();

        return response()->json([
            'message' => 'Video eliminado exitosamente'
        ], 200);
    }
}