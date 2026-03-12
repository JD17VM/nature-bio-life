<?php

namespace App\Http\Requests\Comision;

use Illuminate\Foundation\Http\FormRequest;

class StoreComisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'vendedor_id' => 'required|exists:users,id',
            'comprador_id' => 'required|exists:users,id',
            'pedido_id' => 'required|exists:pedidos,id',
            'monto_compra' => 'required|numeric|min:0',
            'porcentaje' => 'required|numeric|min:0|max:100',
            // monto_comision se puede calcular en el controller, pero permitimos enviarlo
            'monto_comision' => 'required|numeric|min:0', 
            'observaciones' => 'nullable|string'
        ];
    }
}