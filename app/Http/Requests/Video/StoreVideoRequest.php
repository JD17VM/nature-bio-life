<?php

namespace App\Http\Requests\Video;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la petición.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255|unique:videos',
            'descripcion' => 'nullable|string',
            'url' => 'required|string|max:255',
            'thumbnail_url' => 'nullable|string|max:255',
            'duracion_segundos' => 'nullable|integer|min:0',
            'categoria_video_id' => 'required|integer|exists:categoria_videos,id',
            'nivel' => 'nullable|string|max:100',
            'activo' => 'nullable|boolean',
        ];
    }

    /**
     * (Opcional) Define mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'titulo.required' => 'El título del video es obligatorio.',
            'titulo.unique' => 'Ya existe un video con este título.',
            'url.required' => 'La URL del video es obligatoria.',
            'categoria_video_id.required' => 'La categoría del video es obligatoria.',
            'categoria_video_id.exists' => 'La categoría de video seleccionada no es válida.',
        ];
    }
}