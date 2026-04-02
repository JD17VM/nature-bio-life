<?php

namespace App\Http\Requests\Pedido;

use App\Enums\EstadoPedidoEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateEstadoPedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'estado'        => ['required', new Enum(EstadoPedidoEnum::class)],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'estado.required' => 'El campo estado es obligatorio.',
            'estado'          => 'El estado no es válido. Valores permitidos: ' . implode(', ', EstadoPedidoEnum::valores()),
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Solo el administrador puede cambiar el estado de un pedido.');
    }
}
