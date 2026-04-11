<?php

namespace App\Http\Requests\Pedido;

use Illuminate\Foundation\Http\FormRequest;

class StorePedidoRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        // Permitimos el acceso si el usuario está autenticado
        return $this->user() != null;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la petición.
     */
    public function rules(): array
    {
        return [
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|integer|exists:productos,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
            // 'direccion_envio' => 'nullable|string', // Descomentar si agregas este campo a la tabla pedidos
            'comprobante' => 'nullable|string', // Imagen opcional en Base64
            'codigo_transaccion' => 'nullable|string|max:255',
            'notas' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'detalles.required' => 'El pedido debe contener al menos un producto.',
            'detalles.*.producto_id.exists' => 'Uno de los productos seleccionados no existe.',
            'detalles.*.cantidad.min' => 'La cantidad mínima por producto es 1.',
        ];
    }
}