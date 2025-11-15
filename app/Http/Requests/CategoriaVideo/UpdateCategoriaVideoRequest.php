<?php

namespace App\Http\Requests\CategoriaVideo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoriaVideoRequest extends FormRequest
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
        $categoriaVideoId = $this->route('categoria_video')->id;

        return [
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('categoria_videos')->ignore($categoriaVideoId),
            ],
            'descripcion' => 'sometimes|nullable|string',
            'orden' => 'sometimes|nullable|integer',
        ];
    }
    
    /**
     * (Opcional) Define mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe otra categoría de video con este nombre.',
        ];
    }
}