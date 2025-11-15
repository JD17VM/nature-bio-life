<?php

namespace App\Http\Requests\Configuracion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConfiguracionRequest extends FormRequest
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
        $configuracionId = $this->route('configuracione')->id;

        return [
            'clave' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('configuraciones')->ignore($configuracionId),
            ],
            'valor' => 'sometimes|nullable|string',
            'descripcion' => 'sometimes|nullable|string',
        ];
    }

    /**
     * (Opcional) Define mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'clave.unique' => 'Ya existe otra configuración con esta clave.',
        ];
    }
}