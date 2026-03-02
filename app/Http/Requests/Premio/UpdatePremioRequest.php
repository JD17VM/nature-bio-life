<?php

namespace App\Http\Requests\Premio;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePremioRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Obtiene las reglas de validación que se aplican a la petición.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $premioId = $this->route('premio')->id;

        return [
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('premios')->ignore($premioId),
            ],
            'descripcion' => 'sometimes|nullable|string',
            'puntos_requeridos' => 'sometimes|required|integer|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'categoria_premio_id' => 'sometimes|required|integer|exists:categoria_premios,id',
            'imagen_url' => 'sometimes|nullable|string|max:255',
            'disponible' => 'sometimes|boolean',
        ];
    }

    /**
     * (Opcional) Define mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe otro premio con este nombre.',
            'categoria_premio_id.exists' => 'La categoría de premio seleccionada no es válida.',
        ];
    }
}