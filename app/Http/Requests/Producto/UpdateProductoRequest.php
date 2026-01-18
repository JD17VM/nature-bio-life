<?php

namespace App\Http\Requests\Producto;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductoRequest extends FormRequest
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
        $productoId = $this->route('producto')->id;

        return [
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('productos')->ignore($productoId),
            ],
            'descripcion' => 'sometimes|nullable|string',
            'precio' => 'sometimes|required|numeric|decimal:2|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'puntos' => 'sometimes|required|integer|min:0',
            'categoria_id' => 'sometimes|required|integer|exists:categorias,id',
            'imagen' => 'nullable|image|max:2048',
            'destacado' => 'sometimes|boolean',
            'activo' => 'sometimes|boolean',
        ];
    }

     /**
     * (Opcional) Define mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe otro producto con este nombre.',
            'categoria_id.exists' => 'La categoría seleccionada no existe o no es válida.',
            // Puedes añadir más mensajes personalizados si lo deseas
        ];
    }
}