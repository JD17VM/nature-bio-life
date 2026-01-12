<?php

namespace App\Http\Requests\Pedido;

use Illuminate\Foundation\Http\FormRequest;

class StorePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'costo_envio' => 'numeric|min:0',
            'notas' => 'nullable|string',
            
            // Validamos que envíen un ARRAY de productos
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'detalles.required' => 'El pedido debe tener al menos un producto.',
            'detalles.*.producto_id.exists' => 'Uno de los productos no existe.',
        ];
    }
}