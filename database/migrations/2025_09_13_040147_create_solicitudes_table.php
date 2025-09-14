<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $t) {
            $t->bigIncrements('id');

            // Identificación y contacto (normalizados)
            $t->string('ci', 20);                       // solo dígitos (validar en controller)
            $t->string('nombre_completo', 191);
            $t->string('email', 191)->index();
            $t->string('telefono', 30)->nullable();

            // Datos del formulario público
            $t->unsignedTinyInteger('menores_a_cargo')->nullable();
            $t->unsignedTinyInteger('dormitorios')->nullable();
            $t->text('comentarios')->nullable();

            // Flujo de aprobación
            $t->enum('estado', ['pendiente','aprobado','rechazado'])
              ->default('pendiente')
              ->index();

            // Trazabilidad (opcional pero útil para BO)
            $t->unsignedBigInteger('aprobado_por')->nullable();
            $t->timestamp('aprobado_at')->nullable();

            $t->timestamps();

            // (Opcional) evita duplicar pendientes por el mismo email
            // Si no querés esta restricción, comentá la línea siguiente.
            $t->unique(['email', 'estado'], 'solicitudes_email_estado_uk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};