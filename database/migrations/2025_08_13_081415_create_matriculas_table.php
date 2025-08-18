<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matriculas', function (Blueprint $t) {
            $t->id();

            // FK a la ficha y al usuario (aprendiz)
            $t->foreignId('ficha_id')->constrained('fichas')->cascadeOnDelete();
            $t->foreignId('aprendiz_id')->constrained('users')->cascadeOnDelete();

            // Estado y fecha de matrícula
            $t->enum('estado', ['activa','retirada'])->default('activa');
            $t->timestamps();

            // Evitar duplicados del mismo aprendiz en la misma ficha
            $t->unique(['ficha_id','aprendiz_id'], 'uq_matricula_unica');

            // Para listados y filtros
            $t->index('ficha_id');
            $t->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matriculas');
    }
};
