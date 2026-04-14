<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePerfilRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize(): bool
    {
        return true; // Solo usuarios autenticados puede actualizar su perfil
    }

    /**
     * Obtiene las reglas de validación que se aplican a la petición.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre_completo' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore(auth()->id()),
            ],
            'telefono' => 'sometimes|nullable|string|max:20',
            'dni' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
                Rule::unique('users')->ignore(auth()->id()),
            ],
            'direccion' => 'sometimes|nullable|string',
        ];
    }

    /**
     * Define mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe ser válido.',
            'email.unique' => 'Este email ya está en uso.',
            'dni.unique' => 'Este DNI ya está en uso.',
        ];
    }
}
