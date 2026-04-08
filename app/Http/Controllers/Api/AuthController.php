<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Maneja el registro de un nuevo vendedor
     */
    public function register(RegisterUserRequest $request)
    {
        $datosValidados = $request->validated();

        // 2. Buscar al patrocinador (si se proporcionó)
        $patrocinadorId = null;
        if (!empty($datosValidados['codigo_patrocinador'])) {
            $patrocinador = User::where('codigo_referido', $datosValidados['codigo_patrocinador'])->first();
            if ($patrocinador) {
                $patrocinadorId = $patrocinador->id;
            }
        }

        // Generar código único (RF-012)
        do {
            $codigoReferido = Str::upper(Str::random(10));
        } while (User::where('codigo_referido', $codigoReferido)->exists());

        // 3. Crear el usuario
        $user = User::create([
            'nombre_completo' => $datosValidados['nombre_completo'],
            'email'           => $datosValidados['email'],
            'password'        => Hash::make($datosValidados['password']),
            'telefono'        => $datosValidados['telefono'] ?? null,
            'dni'             => $datosValidados['dni'] ?? null,
            'direccion'       => $datosValidados['direccion'] ?? null,
            'patrocinador_id' => $patrocinadorId,
            'codigo_referido' => $codigoReferido,
            'rol'             => User::ROL_VENDEDOR, // Asignamos el rol por defecto
        ]);

        // 4. Generar token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => '¡Vendedor registrado exitosamente!',
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Maneja el inicio de sesión (Email o DNI).
     */
    public function login(LoginUserRequest $request)
    {
        // Se asume que en el request el campo llega como 'email' o un campo genérico 'login'
        $loginField = $request->input('email'); 
        $password = $request->input('password');

        // Determinar si es Email o DNI
        $fieldType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'dni';

        // Intentar autenticar
        if (!Auth::attempt([$fieldType => $loginField, 'password' => $password])) {
            return response()->json([
                'message' => 'Credenciales incorrectas.'
            ], 401);
        }

        // Obtener usuario autenticado
        // Auth::user() ya trae el usuario si el attempt funcionó
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Eliminar tokens anteriores si quieres sesión única (opcional), 
        // o simplemente crear uno nuevo (permite múltiples dispositivos - RF-006 implied)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'message' => 'Perfil obtenido exitosamente',
            'data' => $request->user()
        ], 200);
    }
}