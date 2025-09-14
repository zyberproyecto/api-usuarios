<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class SociosSeeder extends Seeder
{
    public function run(): void
    {
        // Helper para construir password: 3 primeras letras del nombre + 123
        $pwd = fn (string $nombre) => strtolower(mb_substr($nombre, 0, 3)) . '123';

        // Socio APROBADO (puede loguear)
        Usuario::updateOrCreate(
            ['ci_usuario' => '22222222'], // usa tu formato/validación real
            [
                'primer_nombre'   => 'Valentina',
                'primer_apellido' => 'Méndez',
                'email'           => 'socio.aprobado@zyber.test',
                'password'        => Hash::make($pwd('Valentina')), // val123
                'estado_registro' => 'aprobado',
                'rol'             => 'socio', // si tu tabla tiene este campo
            ]
        );

        // Socio PENDIENTE (no debe poder loguear)
        Usuario::updateOrCreate(
            ['ci_usuario' => '33333333'],
            [
                'primer_nombre'   => 'Lucia',
                'primer_apellido' => 'Pereyra',
                'email'           => 'socio.pendiente@zyber.test',
                'password'        => Hash::make($pwd('Lucia')), // luc123
                'estado_registro' => 'pendiente',
                'rol'             => 'socio',
            ]
        );

        // (Opcional) Socio RECHAZADO
        Usuario::updateOrCreate(
            ['ci_usuario' => '44444444'],
            [
                'primer_nombre'   => 'Diego',
                'primer_apellido' => 'Suárez',
                'email'           => 'socio.rechazado@zyber.test',
                'password'        => Hash::make($pwd('Diego')), // die123
                'estado_registro' => 'rechazado',
                'rol'             => 'socio',
            ]
        );
    }
}