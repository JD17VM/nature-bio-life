<?php

namespace App\Http\Requests\Pedido;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        // Solo permitimos actualizar el estado o notas
        return [
            'estado' => 'required|string|in:pendiente,pagado,enviado,entregado,cancelado',
            'notas' => 'nullable|string',
        ];
    }
}