<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PerfilController extends Controller
{
    /**
     * Perfil del usuario autenticado (alias de /me).
     */
    public function perfil(Request $request)
    {
        $u = $request->user();
        if (!$u) {
            return response()->json(['ok' => false], 401);
        }

        $rol    = strtolower((string)($u->rol ?? 'socio')); // usamos la columna 'rol'
        $estado = $u->estado_registro ?? $u->estado ?? 'aprobado';

        return response()->json([
            'ok'   => true,
            'data' => [
                'id'         => $u->id,
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