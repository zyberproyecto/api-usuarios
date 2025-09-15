<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Mantengo el tuyo (SociosSeeder) y agrego perfil + solicitudes
        $this->call([
            SociosSeeder::class,
            UsuariosPerfilSeeder::class,
            SolicitudSeeder::class,
        ]);
    }
}