<?php

namespace App\Http\Controllers;

use App\Models\UsuarioPerfil;
use Illuminate\Http\Request;

class PerfilController extends Controller
{
    /**
     * Alias de /me (tu método existente). NO SE TOCA.
     * Devuelve datos básicos del usuario autenticado.
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

    /**
     * GET /api/perfil  (auth:sanctum)
     * Datos básicos + perfil extendido (si no existe, fields = null y estado_revision = 'incompleto').
     */
    public function show(Request $request)
    {
        $u = $request->user();
        if (!$u) {
            return response()->json(['ok' => false, 'message' => 'No autenticado'], 401);
        }

        // CI normalizada (solo dígitos)
        $ci = preg_replace('/\D/', '', (string)($u->ci_usuario ?? $u->ci ?? ''));
        if ($ci === '') {
            return response()->json(['ok' => false, 'message' => 'Usuario inválido'], 401);
        }

        $perfil = UsuarioPerfil::find($ci);

        return response()->json([
            'ok' => true,
            'usuario' => [
                'ci_usuario'      => $u->ci_usuario,
                'email'           => $u->email ?? null,
                'telefono'        => $u->telefono ?? null,
                'estado_registro' => $u->estado_registro ?? 'pendiente',
                'nombre'          => $u->nombre
                                       ?? ($u->primer_nombre ?? null)
                                       ?? ($u->name ?? null),
            ],
            'perfil' => [
                'ocupacion'                 => $perfil->ocupacion                   ?? null,
                'ingresos_nucleo_familiar'  => $perfil->ingresos_nucleo_familiar    ?? null,
                'integrantes_familia'       => $perfil->integrantes_familia         ?? null,
                'contacto'                  => $perfil->contacto                    ?? null,
                'direccion'                 => $perfil->direccion                   ?? null,
                'acepta_declaracion_jurada' => $perfil->acepta_declaracion_jurada   ?? null,
                'acepta_reglamento_interno' => $perfil->acepta_reglamento_interno   ?? null,
                'estado_revision'           => $perfil->estado_revision             ?? 'incompleto',
            ],
        ]);
    }

    /**
     * PUT /api/perfil  (auth:sanctum)
     * Crea/actualiza el perfil extendido. TODOS los campos son obligatorios.
     * Cada edición vuelve el estado a 'pendiente' para revisión del Backoffice.
     */
    public function update(Request $request)
    {
        $u = $request->user();
        if (!$u) {
            return response()->json(['ok' => false, 'message' => 'No autenticado'], 401);
        }

        $ci = preg_replace('/\D/', '', (string)($u->ci_usuario ?? $u->ci ?? ''));
        if ($ci === '') {
            return response()->json(['ok' => false, 'message' => 'Usuario inválido'], 401);
        }

        $data = $request->validate([
            'ocupacion'                => ['required','string','max:100'],
            'ingresos_nucleo_familiar' => ['required','numeric','min:0','max:9999999999.99'],
            'integrantes_familia'      => ['required','integer','min:1','max:20'],
            'contacto'                 => ['required','string','max:191'],
            'direccion'                => ['required','string','max:191'],
            // Deben venir aceptadas (true/1/'on')
            'acepta_declaracion_jurada' => ['required','accepted'],
            'acepta_reglamento_interno' => ['required','accepted'],
        ], [
            'acepta_declaracion_jurada.accepted' => 'Debes aceptar la declaración jurada.',
            'acepta_reglamento_interno.accepted' => 'Debes aceptar el reglamento interno.',
        ]);

        // Normalizo a booleanos explícitos
        $data['acepta_declaracion_jurada'] = (bool) $request->boolean('acepta_declaracion_jurada');
        $data['acepta_reglamento_interno'] = (bool) $request->boolean('acepta_reglamento_interno');

        // Upsert: cada edición vuelve a 'pendiente'
        $perfil = UsuarioPerfil::updateOrCreate(
            ['ci_usuario' => $ci],
            array_merge($data, [
                'estado_revision' => 'pendiente',
                'aprobado_por'    => null,
                'aprobado_at'     => null,
            ])
        );

        return response()->json(['ok' => true, 'perfil' => $perfil]);
    }
}