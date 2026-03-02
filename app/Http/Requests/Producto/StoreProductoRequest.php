<?php

namespace App\Http\Requests\Producto;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
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
            'nombre' => 'required|string|max:255|unique:productos',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|decimal:2|min:0',
            'stock' => 'required|integer|min:0',
            'puntos' => 'required|integer|min:0',
            'categoria_id' => 'required|integer|exists:categorias,id',
            'imagen' => 'nullable|image|max:2048',
            'destacado' => 'nullable|boolean',
            'activo' => 'nullable|boolean',
        ];
    }

    /**
     * (Opcional) Define mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.unique' => 'Ya existe un producto con este nombre.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.decimal' => 'El precio debe tener 2 decimales.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'puntos.required' => 'Los puntos son obligatorios.',
            'puntos.integer' => 'Los puntos deben ser un número entero.',
            'categoria_id.required' => 'La categoría es obligatoria.',
            'categoria_id.exists' => 'La categoría seleccionada no existe o no es válida.',
        ];
    }
}