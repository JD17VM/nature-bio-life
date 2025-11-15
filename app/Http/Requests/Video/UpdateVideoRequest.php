<?php

namespace App\Http\Requests\Video;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVideoRequest extends FormRequest
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
        $videoId = $this->route('video')->id;

        return [
            'titulo' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('videos')->ignore($videoId),
            ],
            'descripcion' => 'sometimes|nullable|string',
            'url' => 'sometimes|required|string|max:255',
            'thumbnail_url' => 'sometimes|nullable|string|max:255',
            'duracion_segundos' => 'sometimes|nullable|integer|min:0',
            'categoria_video_id' => 'sometimes|required|integer|exists:categoria_videos,id',
            'nivel' => 'sometimes|nullable|string|max:100',
            'activo' => 'sometimes|boolean',
        ];
    }

    /**
     * (Opcional) Define mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'titulo.unique' => 'Ya existe otro video con este título.',
            'categoria_video_id.exists' => 'La categoría de video seleccionada no es válida.',
        ];
    }
}