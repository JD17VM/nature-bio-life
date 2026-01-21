<?php

namespace App\Http\Requests\Material;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
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
            'titulo' => 'required|string|max:255|unique:materiales',
            'descripcion' => 'nullable|string',
            'archivo' => 'required|file|max:20480',
            'tipo_material_id' => 'required|integer|exists:tipo_materiales,id',
            'activo' => 'nullable|boolean',
        ];
    }

    /**
     * (Opcional) Define mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'titulo.required' => 'El título del material es obligatorio.',
            'titulo.unique' => 'Ya existe un material con este título.',
            'archivo.required' => 'El archivo es obligatorio.',
            'tipo_material_id.required' => 'El tipo de material es obligatorio.',
            'tipo_material_id.exists' => 'El tipo de material seleccionado no es válido.',
        ];
    }
}