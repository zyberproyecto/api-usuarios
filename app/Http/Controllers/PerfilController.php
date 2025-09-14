<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PerfilController extends Controller
{
    /**
     * Perfil del usuario autenticado (alias de /me).
     * Requiere auth:sanctum.
     * Respuesta consistente con AuthController::me()
     */
    public function perfil(Request $request)
    {
        $u = $request->user();
        if (!$u) {
            return response()->json(['ok' => false], 401);
        }

        $rol    = 'socio'; // Esta API es solo para socios
        $estado = $u->estado_registro ?? $u->estado ?? 'aprobado';

        return response()->json([
            'ok'   => true,
            'user' => [
                'id'         => $u->id ?? null,
                'rol'        => $rol,
                'estado'     => $estado,
                'ci_usuario' => $u->ci_usuario ?? ($u->ci ?? null),
                'email'      => $u->email ?? null,
                'nombre'     => $u->nombre
                                 ?? ($u->primer_nombre ?? null)
                                 ?? ($u->name ?? null),
            ],
        ]);
    }
}