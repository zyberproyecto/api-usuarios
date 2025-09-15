<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsuariosPerfilSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // APROBADO 22222222
        DB::table('usuarios_perfil')->updateOrInsert(
            ['ci_usuario' => '22222222'],
            [
                'ocupacion'                  => 'Analista',
                'ingresos_nucleo_familiar'   => 78000.00,
                'integrantes_familia'        => 3,
                'contacto'                   => '098111111',
                'direccion'                  => 'Av. Principal 123',
                'acepta_declaracion_jurada'  => 1,
                'acepta_reglamento_interno'  => 1,
                'estado_revision'            => 'aprobado',
                'aprobado_por'               => null,
                'aprobado_at'                => $now,
                'created_at'                 => $now,
                'updated_at'                 => $now,
            ]
        );

        // PENDIENTE 33333333
        DB::table('usuarios_perfil')->updateOrInsert(
            ['ci_usuario' => '33333333'],
            [
                'ocupacion'                  => 'Operario',
                'ingresos_nucleo_familiar'   => 52000.00,
                'integrantes_familia'        => 4,
                'contacto'                   => '098222222',
                'direccion'                  => 'Calle Secundaria 456',
                'acepta_declaracion_jurada'  => 1,
                'acepta_reglamento_interno'  => 1,
                'estado_revision'            => 'pendiente',
                'aprobado_por'               => null,
                'aprobado_at'                => null,
                'created_at'                 => $now,
                'updated_at'                 => $now,
            ]
        );

        // RECHAZADO 44444444
        DB::table('usuarios_perfil')->updateOrInsert(
            ['ci_usuario' => '44444444'],
            [
                'ocupacion'                  => 'Independiente',
                'ingresos_nucleo_familiar'   => 30000.00,
                'integrantes_familia'        => 2,
                'contacto'                   => '098333333',
                'direccion'                  => 'Ruta 8 km 25',
                'acepta_declaracion_jurada'  => 1,
                'acepta_reglamento_interno'  => 1,
                'estado_revision'            => 'rechazado',
                'aprobado_por'               => null,
                'aprobado_at'                => null,
                'created_at'                 => $now,
                'updated_at'                 => $now,
            ]
        );
    }
}