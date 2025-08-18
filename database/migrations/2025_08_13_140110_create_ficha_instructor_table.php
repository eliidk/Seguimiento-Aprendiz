<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ficha_instructor', function (Blueprint $t) {
    $t->id();

    $t->foreignId('ficha_id')->constrained('fichas')->cascadeOnDelete();
    $t->foreignId('instructor_id')->constrained('users')->cascadeOnDelete(); 

    $t->timestamps();

    // Evita duplicados del mismo instructor en la misma ficha
    $t->unique(['ficha_id','instructor_id'], 'uq_ficha_instructor');

    // Listados rápidos
    $t->index('instructor_id');
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ficha_instructor');
    }
};
