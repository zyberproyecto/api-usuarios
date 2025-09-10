<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->bigIncrements('id');

            // CI como identificador natural
            $table->string('ci_usuario', 20)->unique();

            // Datos personales
            $table->string('primer_nombre', 100)->nullable();
            $table->string('segundo_nombre', 100)->nullable();
            $table->string('primer_apellido', 100)->nullable();
            $table->string('segundo_apellido', 100)->nullable();
            $table->string('nombre', 200)->nullable(); // nombre completo opcional

            $table->string('email', 190)->unique();
            $table->string('telefono', 50)->nullable();

            // Autenticación
            $table->string('password', 255); // obligatorio, nunca null
            $table->rememberToken();

            // Estado del registro (minúsculas)
            $table->enum('estado_registro', ['pendiente', 'aprobado', 'rechazado'])
                  ->default('pendiente');

            // Rol
            $table->enum('rol', ['admin', 'socio'])->default('socio');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('usuarios');
    }
};