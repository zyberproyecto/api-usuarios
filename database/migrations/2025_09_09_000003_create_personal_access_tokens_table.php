<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $t) {
            $t->id();

            // IMPORTANTE: tokenable_id como STRING porque usuarios.ci_usuario es string
            $t->string('tokenable_type');
            $t->string('tokenable_id');                      // <- antes era BIGINT
            $t->index(['tokenable_type', 'tokenable_id']);

            $t->string('name');
            $t->string('token', 64)->unique();               // Sanctum guarda hash de 64 chars
            $t->text('abilities')->nullable();
            $t->timestamp('last_used_at')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};