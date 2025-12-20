<?php

namespace App\Http\Requests\CanjePremio;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCanjePremioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'estado' => 'required|string|in:pendiente,aprobado,entregado,rechazado',
            'fecha_entrega' => 'nullable|date',
            'observaciones' => 'nullable|string'
        ];
    }
}