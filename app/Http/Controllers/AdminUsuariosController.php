<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AdminUsuariosController extends Controller
{
    /**
     * Listar usuarios con registro pendiente (solo admin).
     * Soporta datos viejos con "Pendiente" capitalizado.
     */
    public function pendientes(Request $request)
    {
        // Defensa extra (ya está protegido por middleware abilities:admin)
        $u = $request->user();
        if (!$u || !$u->tokenCan('admin')) {
            return response()->json(['ok' => false, 'error' => 'No autorizado'], 403);
        }

        $data = Usuario::whereIn('estado_registro', ['pendiente', 'Pendiente'])->get();

        return response()->json(['ok' => true, 'data' => $data]);
    }

    /**
     * Cambiar estado_registro a pendiente|aprobado|rechazado (solo admin).
     * Admite identificar usuario por ci_usuario o id.
     * Normaliza a minúsculas para consistencia.
     */
    public function setEstado(Request $request, string $identificador)
    {
        // Defensa extra (ya está protegido por middleware abilities:admin)
        $u = $request->user();
        if (!$u || !$u->tokenCan('admin')) {
            return response()->json(['ok' => false, 'error' => 'No autorizado'], 403);
        }

        $request->validate([
            'estado' => ['required', Rule::in(['pendiente', 'aprobado', 'rechazado'])],
        ]);

        // Buscar por CI o por ID
        $usuario = Usuario::where('ci_usuario', $identificador)->first();
        if (!$usuario && ctype_digit($identificador)) {
            $usuario = Usuario::find((int) $identificador);
        }
        if (!$usuario) {
            return response()->json(['ok' => false, 'error' => 'Usuario no encontrado'], 404);
        }

        $estado = Str::lower($request->estado); // normaliza
        $usuario->estado_registro = $estado;
        $usuario->save();

        return response()->json(['ok' => true, 'data' => $usuario]);
    }
}