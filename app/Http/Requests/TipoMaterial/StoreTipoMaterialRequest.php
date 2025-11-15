<?php

namespace App\Http\Requests\TipoMaterial;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoMaterialRequest extends FormRequest
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
            'nombre' => 'required|string|max:255|unique:tipo_materiales',
            'extension_permitida' => 'nullable|string|max:50',
        ];
    }

    /**
     * (Opcional) Define mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del tipo de material es obligatorio.',
            'nombre.unique' => 'Ya existe un tipo de material con este nombre.',
        ];
    }
}