<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llamamos al AdminSeeder para asegurar que exista al menos un admin
        $this->call(AdminSeeder::class);
        $this->call(\Database\Seeders\SocioSeeder::class); // <--- NUEVO
    }
}
