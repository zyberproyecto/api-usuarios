<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $ci = '99999999';

        Usuario::updateOrCreate(
            ['ci_usuario' => $ci],
            [
                'primer_nombre'   => 'Admin',
                'primer_apellido' => 'Coop',
                'email'           => 'admin@coop.test',
                'password'        => Hash::make('admin123'),
                'estado_registro' => 'aprobado', // en minúsculas
                'rol'             => 'admin',
            ]
        );
    }
}