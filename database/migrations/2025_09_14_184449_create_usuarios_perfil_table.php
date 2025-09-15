<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('usuarios_perfil')) {
            Schema::create('usuarios_perfil', function (Blueprint $t) {
                $t->engine    = 'InnoDB';
                $t->charset   = 'utf8mb4';
                $t->collation = 'utf8mb4_unicode_ci';

                // PK = ci_usuario (coincide con usuarios.ci_usuario, VARCHAR(8))
                $t->string('ci_usuario', 8)->primary()->collation('utf8mb4_unicode_ci');

                // ===== DATOS EXTRAS (TODOS OBLIGATORIOS) =====
                $t->string('contacto', 191);                    // obligatorio (nombre/teléfono de contacto)
                $t->string('direccion', 191);                   // obligatorio
                $t->string('ocupacion', 100);                   // obligatorio
                $t->decimal('ingresos_nucleo_familiar', 12, 2); // obligatorio
                $t->unsignedTinyInteger('integrantes_familia'); // obligatorio


                // Aceptaciones (checkboxes obligatorias)
                $t->boolean('acepta_declaracion_jurada');       // debe venir en 1 desde el frontend
                $t->boolean('acepta_reglamento_interno');       // debe venir en 1 desde el frontend

                // Revisión por Backoffice
                $t->enum('estado_revision', ['pendiente','aprobado','rechazado'])->default('pendiente');
                $t->unsignedBigInteger('aprobado_por')->nullable();
                $t->timestamp('aprobado_at')->nullable();

                $t->timestamps();

                // FK hacia usuarios.ci_usuario
                $t->foreign('ci_usuario')
                  ->references('ci_usuario')->on('usuarios')
                  ->onDelete('cascade');

                $t->index(['estado_revision']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios_perfil');
    }
};