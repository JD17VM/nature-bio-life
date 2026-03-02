<?php

namespace App\Http\Requests\HistorialPuntos;

use Illuminate\Foundation\Http\FormRequest;

class StoreHistorialPuntosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'pedido_id' => 'nullable|exists:pedidos,id',
            'puntos' => 'required|integer', // Puede ser negativo
            'tipo' => 'required|string|in:ingreso,egreso',
            'descripcion' => 'nullable|string'
        ];
    }
}