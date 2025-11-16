<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Maneja el registro de un nuevo vendedor.
     */
    public function register(RegisterUserRequest $request)
    {
        // 1. La validación ya pasó (gracias a RegisterUserRequest)
        $datosValidados = $request->validated();

        // 2. Buscar al patrocinador (si se proprocionó un código)
        $patrocinadorId = null;
        if ($request->filled('codigo_patrocinador')) {
            $patrocinador = User::where('codigo_referido', $datosValidados['codigo_patrocinador'])->first();
            if ($patrocinador) {
                $patrocinadorId = $patrocinador->id;
            }
        }

        // 3. Crear el usuario
        $user = User::create([
            'nombre_completo' => $datosValidados['nombre_completo'],
            'email' => $datosValidados['email'],
            'password' => Hash::make($datosValidados['password']),
            'telefono' => $datosValidados['telefono'] ?? null,
            'dni' => $datosValidados['dni'] ?? null,
            'direccion' => $datosValidados['direccion'] ?? null,
            'patrocinador_id' => $patrocinadorId,
            'codigo_referido' => Str::random(10), // Generamos un código de referido único
        ]);

        // 4. (Opcional) Generar un token inmediatamente después del registro
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => '¡Vendedor registrado exitosamente!',
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Maneja el inicio de sesión del vendedor.
     */
    public function login(LoginUserRequest $request)
    {
        // 1. La validación ya pasó (gracias a LoginUserRequest)
        $datosValidados = $request->validated();

        // 2. Intentar autenticar al usuario
        if (!Auth::attempt($datosValidados)) {
            return response()->json([
                'message' => 'Email o contraseña incorrectos.'
            ], 401); // 401 = No autorizado
        }

        // 3. Si las credenciales son correctas, buscar al usuario
        $user = User::where('email', $datosValidados['email'])->first();

        // 4. Crear y devolver el token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }
}