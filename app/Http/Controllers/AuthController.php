<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /** Busca usuario por email o por CI estricta (7–8 dígitos) */
    private function findUserByLogin(string $login): ?Usuario
    {
        $login = trim($login);

        // Si es email, buscamos por email
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return Usuario::where('email', $login)->first();
        }

        // Si NO es email, debe ser CI estricta (solo 7–8 dígitos, sin puntos ni guiones)
        if (!preg_match('/^\d{7,8}$/', $login)) {
            // error de formato (rechazar entradas con puntos/guiones/letras)
            throw ValidationException::withMessages([
                'login' => 'La CI debe ingresarse sin puntos ni guiones (solo 7 u 8 dígitos).',
            ]);
        }

        // Comparamos contra la CI en BD reducida a dígitos (MySQL 8)
        // Ej: "4.321.987-6" -> "43219876"
        return Usuario::whereRaw(
            "REGEXP_REPLACE(ci_usuario, '[^0-9]', '') = ?",
            [$login]
        )->first();
    }

    /**
     * Login SOLO para socios (por email o CI estricta).
     * Requisitos:
     *  - rol = 'socio'
     *  - estado_registro = 'aprobado'
     * Devuelve token Sanctum con ability ['socio'].
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'login'    => ['required','string'],
            'password' => ['required','string'],
        ]);

        // Buscar usuario
        $user = $this->findUserByLogin($data['login']);

        if (!$user) {
            throw ValidationException::withMessages([
                'login' => 'Usuario no encontrado.',
            ]);
        }

        // Password (solo hash válido de Laravel)
        if (!Hash::check($data['password'], (string) $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Credenciales inválidas.',
            ]);
        }

        // 🔒 Solo socios
        if (mb_strtolower((string)($user->rol ?? '')) !== 'socio') {
            return response()->json([
                'ok'   => false,
                'error'=> 'Solo socios pueden iniciar sesión.',
            ], 403);
        }

        // 🔒 Solo aprobados (acepta sinónimos comunes)
        $estado     = $user->estado_registro ?? $user->estado ?? null;
        $estadoNorm = $estado ? mb_strtolower($estado) : null;
        $aprobado   = in_array($estadoNorm, ['aprobado','aprobada','ok','activo','activa'], true);

        if (!$aprobado) {
            return response()->json([
                'ok'     => false,
                'error'  => 'Usuario no aprobado aún.',
                'estado' => $estado ?? 'pendiente',
            ], 403);
        }

        // Token con ability 'socio'
        $token = $user->createToken('socio-token', ['socio'])->plainTextToken;

        return response()->json([
            'ok'    => true,
            'token' => $token,
            'user'  => [
                'id'         => $user->id ?? null,
                'rol'        => 'socio',
                'estado'     => $estadoNorm ?? 'aprobado',
                'ci_usuario' => $user->ci_usuario,
                'email'      => $user->email,
                'nombre'     => $user->nombre
                                  ?? ($user->primer_nombre ?? null)
                                  ?? ($user->name ?? null),
            ],
        ]);
    }

    /** Perfil del usuario autenticado (token Sanctum) */
    public function me(Request $request)
    {
        $u = $request->user();
        if (!$u) return response()->json(['ok' => false], 401);

        $estado = $u->estado_registro ?? $u->estado ?? 'aprobado';

        return response()->json([
            'ok'   => true,
            'user' => [
                'id'         => $u->id ?? null,
                'rol'        => 'socio',
                'estado'     => $estado,
                'ci_usuario' => $u->ci_usuario ?? null,
                'email'      => $u->email ?? null,
                'nombre'     => $u->nombre
                                  ?? ($u->primer_nombre ?? null)
                                  ?? ($u->name ?? null),
            ],
        ]);
    }

    /** Logout del token actual */
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
        return response()->json(['ok' => true]);
    }
}