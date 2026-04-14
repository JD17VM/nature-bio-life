<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\RolUsuarioEnum;
use App\Models\User;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\UpdatePerfilRequest;
use App\Http\Requests\Auth\CambiarPasswordRequest;
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
            'rol'             => RolUsuarioEnum::VENDEDOR,
        ]);

        // 4. Generar token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => '¡Vendedor registrado exitosamente!',
            'user'         => $this->formatearUsuario($user),
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 201);
    }

    /**
     * Maneja el inicio de sesión (Email o DNI).
     */
    public function login(LoginUserRequest $request)
    {
        $loginField = $request->input('email');
        $password   = $request->input('password');

        $fieldType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'dni';

        if (!Auth::attempt([$fieldType => $loginField, 'password' => $password])) {
            return response()->json([
                'message' => 'Credenciales incorrectas.'
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Inicio de sesión exitoso',
            'user'         => $this->formatearUsuario($user),
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 200);
    }

    /**
     * Retorna el perfil del usuario autenticado.
     */
    public function profile(Request $request)
    {
        return response()->json([
            'message' => 'Perfil obtenido exitosamente',
            'user'    => $this->formatearUsuario($request->user()),
        ], 200);
    }

    /**
     * Cierra la sesión revocando el token actual.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente.',
        ], 200);
    }

    /**
     * Actualiza el perfil del usuario autenticado.
     */
    public function updateProfile(UpdatePerfilRequest $request)
    {
        $user = $request->user();
        $datosValidados = $request->validated();

        // Solo actualizar campos que se enviaron
        $user->update($datosValidados);

        return response()->json([
            'message' => 'Perfil actualizado exitosamente',
            'user'    => $this->formatearUsuario($user),
        ], 200);
    }

    /**
     * Cambia la contraseña del usuario autenticado.
     */
    public function changePassword(CambiarPasswordRequest $request)
    {
        $user = $request->user();
        $datosValidados = $request->validated();

        // Actualizar la contraseña
        $user->update([
            'password' => Hash::make($datosValidados['new_password']),
        ]);

        return response()->json([
            'message' => 'Contraseña actualizada exitosamente',
        ], 200);
    }

    /**
     * Devuelve los campos del usuario que siempre debe recibir el frontend.
     */
    private function formatearUsuario(User $user): array
    {
        return [
            'id'              => $user->id,
            'nombre_completo' => $user->nombre_completo,
            'email'           => $user->email,
            'telefono'        => $user->telefono,
            'dni'             => $user->dni,
            'direccion'       => $user->direccion,
            'rol'             => $user->rol?->value ?? $user->getRawOriginal('rol'),
            'rol_label'       => $user->rol?->label() ?? $user->getRawOriginal('rol'),
            'codigo_referido' => $user->codigo_referido,
            'patrocinador_id' => $user->patrocinador_id,
            'puntos_saldo'    => $user->puntos_saldo,
            'activo'          => $user->activo,
            'created_at'      => $user->created_at,
        ];
    }
}