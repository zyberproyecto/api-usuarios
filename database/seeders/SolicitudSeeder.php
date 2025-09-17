<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SolicitudSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

       DB::table('solicitudes')->insertOrIgnore([
  [
    'ci'              => '33333333',
    'nombre_completo' => 'Lucia Pereyra',
    'email'           => 'socio.pendiente@zyber.test',
    'telefono'        => '099222222',
    'menores_a_cargo' => 1,
    'dormitorios'     => 3,
    'comentarios'     => 'Quisiera más info.',
    'estado'          => 'pendiente',
    'aprobado_por'    => null,
    'aprobado_at'     => null,
    'created_at'      => $now,
    'updated_at'      => $now,
  ],
  [
    'ci'              => '55555555',
    'nombre_completo' => 'Nuevo Postulante',
    'email'           => 'nuevo@postulante.test',
    'telefono'        => '097555555',
    'menores_a_cargo' => 0,
    'dormitorios'     => 2,
    'comentarios'     => 'Me interesa 2 dormitorios.',
    'estado'          => 'pendiente',
    'aprobado_por'    => null,
    'aprobado_at'     => null,
    'created_at'      => $now,
    'updated_at'      => $now,
  ],
]);
     
    }
}