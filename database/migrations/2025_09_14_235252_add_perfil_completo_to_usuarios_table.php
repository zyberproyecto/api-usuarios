<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            // Agregar sólo si no existe
            if (!Schema::hasColumn('usuarios', 'perfil_completo')) {
                $table->boolean('perfil_completo')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (Schema::hasColumn('usuarios', 'perfil_completo')) {
                $table->dropColumn('perfil_completo');
            }
        });
    }
};