<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class SocioSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::updateOrCreate(
            ['ci_usuario' => '12345678'], // CI de prueba
            [
                'primer_nombre'   => 'Socio',
                'primer_apellido' => 'Demo',
                'email'           => 'socio@coop.test',
                'password'        => Hash::make('socio123'),
                'estado_registro' => 'aprobado', // así ya puede logear
                'rol'             => 'socio',
            ]
        );
    }
}