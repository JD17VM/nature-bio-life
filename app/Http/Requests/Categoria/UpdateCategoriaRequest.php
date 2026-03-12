<?php

namespace App\Http\Requests\Categoria;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoriaRequest extends FormRequest
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
        // Obtenemos el ID de la categoría desde la ruta (ej: /api/categorias/1)
        $categoriaId = $this->route('categoria')->id;

        return [
            'nombre' => [
                'sometimes', // 'sometimes' = validar solo si está presente en el request
                'required',
                'string',
                'max:255',
                Rule::unique('categorias')->ignore($categoriaId),
            ],
            'descripcion' => 'sometimes|nullable|string',
            'activa' => 'sometimes|boolean',
        ];
    }

    /**
     * (Opcional) Define mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe otra categoría con este nombre.',
        ];
    }
}