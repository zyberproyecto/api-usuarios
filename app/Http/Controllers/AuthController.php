<?php 

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login por email o CI (ci_usuario).
     * Devuelve token Sanctum con abilities según el rol.
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $login = trim($data['login']);
        $pass  = $data['password'];

        // --------- 1) ADMIN ----------
        $admin = Usuario::query()
            ->where('rol', 'admin')
            ->where(function ($q) use ($login) {
                $q->where('email', $login)
                  ->orWhere('ci_usuario', $login);
            })
            ->first();

        if ($admin && $this->passwordMatch($pass, $admin->password)) {
            $estado = $admin->estado_registro ?? $admin->estado ?? null;
            if (!$this->isApproved($estado)) {
                return response()->json([
                    'ok'     => false,
                    'error'  => 'Usuario no aprobado aún.',
                    'estado' => $estado ?? 'pendiente',
                ], 403);
            }

            // ability para rutas admin en api-cooperativa / backoffice
            $token = $admin->createToken('api', ['admin'])->plainTextToken;

            return response()->json([
                'ok'    => true,
                'token' => $token,
                'user'  => [
                    'id'         => $admin->id,
                    'rol'        => 'admin',
                    'estado'     => $estado ?? 'aprobado',
                    'ci_usuario' => $admin->ci_usuario,
                    'email'      => $admin->email,
                    'nombre'     => $admin->nombre
                                    ?? ($admin->primer_nombre ?? null)
                                    ?? ($admin->name ?? null),
                ],
            ]);
        }

        // --------- 2) USUARIO / SOCIO ----------
        $socio = Usuario::query()
            ->where('ci_usuario', $login)
            ->orWhere('email', $login)
            ->first();

        if (!$socio) {
            throw ValidationException::withMessages([
                'login' => 'Usuario no encontrado.',
            ]);
        }

        if (!$this->passwordMatch($pass, $socio->password)) {
            throw ValidationException::withMessages([
                'login' => 'Credenciales inválidas.',
            ]);
        }

        $estado = $socio->estado_registro ?? $socio->estado ?? null;
        if (!$this->isApproved($estado)) {
            return response()->json([
                'ok'     => false,
                'error'  => 'Usuario no aprobado aún.',
                'estado' => $estado ?? 'pendiente',
            ], 403);
        }

        // 👉 usar el rol real del usuario encontrado
        $rol = $socio->rol ?: 'socio';
        $ability = ($rol === 'admin') ? 'admin' : 'socio';

        $token = $socio->createToken('api', [$ability])->plainTextToken;

        return response()->json([
            'ok'    => true,
            'token' => $token,
            'user'  => [
                'id'           => $socio->id,
                'rol'          => $rol,
                'estado'       => $estado ?? 'aprobado',
                'ci_usuario'   => $socio->ci_usuario,
                'email'        => $socio->email,
                'nombre'       => $socio->nombre
                                   ?? ($socio->primer_nombre ?? null)
                                   ?? ($socio->name ?? null),
            ],
        ]);
    }

    /**
     * Datos del usuario autenticado.
     */
    public function me(Request $request)
    {
        $u = $request->user();
        if (!$u) {
            return response()->json(['ok' => false], 401);
        }

        $estado = $u->estado_registro ?? $u->estado ?? 'aprobado';

        return response()->json([
            'ok'   => true,
            'user' => [
                'id'         => $u->id,
                'rol'        => strtolower((string)($u->rol ?? 'socio')),
                'estado'     => $estado,
                'ci_usuario' => $u->ci_usuario ?? null,
                'email'      => $u->email ?? null,
                'nombre'     => $u->nombre
                                ?? ($u->primer_nombre ?? null)
                                ?? ($u->name ?? null),
            ],
        ]);
    }

    /**
     * Logout del token actual.
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json(['ok' => true]);
    }

    // ----------------- Helpers -----------------

    private function passwordMatch(string $plain, ?string $stored): bool
    {
        if (!$stored) {
            return false;
        }
        // Soporta hashed y texto plano (para ambientes de prueba)
        return Hash::check($plain, $stored) || hash_equals($stored, $plain);
    }

    private function isApproved(?string $estado): bool
    {
        if (!$estado) {
            // Si no hay columna en la versión actual, dejamos pasar (compat)
            return true;
        }
        $e = mb_strtolower($estado);
        return in_array($e, ['aprobado', 'aprobada', 'ok', 'activo', 'activa'], true);
    }
}