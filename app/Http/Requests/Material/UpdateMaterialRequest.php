<?php

namespace App\Http\Requests\Material;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMaterialRequest extends FormRequest
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
        // El parámetro de ruta singular de 'materiales' es 'materiale'
        $materialId = $this->route('materiale')->id;

        return [
            'titulo' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('materiales')->ignore($materialId),
            ],
            'descripcion' => 'sometimes|nullable|string',
            'archivo' => 'nullable|file|max:20480',
            'tipo_material_id' => 'sometimes|required|integer|exists:tipo_materiales,id',
            'activo' => 'sometimes|boolean',
        ];
    }

    /**
     * (Opcional) Define mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'titulo.unique' => 'Ya existe otro material con este título.',
            'tipo_material_id.exists' => 'El tipo de material seleccionado no es válido.',
        ];
    }
}