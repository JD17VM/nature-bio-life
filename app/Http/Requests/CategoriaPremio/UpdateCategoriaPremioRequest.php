<?php

namespace App\Http\Requests\CategoriaPremio;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoriaPremioRequest extends FormRequest
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
        // Ojo: el nombre del parámetro en la ruta es 'categoria_premio'
        // Laravel 11 lo infiere, pero podemos ser explícitos.
        $categoriaPremioId = $this->route('categoria_premio')->id;

        return [
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('categoria_premios')->ignore($categoriaPremioId),
            ],
            'descripcion' => 'sometimes|nullable|string',
        ];
    }

    /**
     * (Opcional) Define mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe otra categoría de premio con este nombre.',
        ];
    }
}