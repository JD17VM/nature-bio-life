<?php

namespace App\Http\Requests\CanjePremio;

use Illuminate\Foundation\Http\FormRequest;

class StoreCanjePremioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'premio_id' => 'required|exists:premios,id',
            // No pedimos puntos, los sacamos del premio en el backend por seguridad
            'observaciones' => 'nullable|string'
        ];
    }
}