<?php

namespace App\Http\Requests\Premio;

use Illuminate\Foundation\Http\FormRequest;

class StorePremioRequest extends FormRequest
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
        return [
            'nombre' => 'required|string|max:255|unique:premios',
            'descripcion' => 'nullable|string',
            'puntos_requeridos' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'categoria_premio_id' => 'required|integer|exists:categoria_premios,id',
            'imagen_url' => 'nullable|string|max:255',
            'disponible' => 'nullable|boolean',
        ];
    }

    /**
     * (Opcional) Define mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del premio es obligatorio.',
            'nombre.unique' => 'Ya existe un premio con este nombre.',
            'puntos_requeridos.required' => 'Los puntos requeridos son obligatorios.',
            'categoria_premio_id.required' => 'La categoría del premio es obligatoria.',
            'categoria_premio_id.exists' => 'La categoría de premio seleccionada no es válida.',
        ];
    }
}