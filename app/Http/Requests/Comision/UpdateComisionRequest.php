<?php

namespace App\Http\Requests\Comision;

use Illuminate\Foundation\Http\FormRequest;

class UpdateComisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'estado' => 'required|string|in:pendiente,aprobada,pagada,anulada',
            'fecha_pago' => 'nullable|date',
            'observaciones' => 'nullable|string'
        ];
    }
}