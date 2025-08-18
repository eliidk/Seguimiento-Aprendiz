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
          Schema::create('asistencias', function (Blueprint $table) {
$table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('matricula_id');
            $table->enum('estado', ['Presente', 'Ausente', 'Justificado', 'Tarde']);
            $table->text('nota')->nullable();
            $table->timestamps();

            // Relaciones
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('matricula_id')->references('id')->on('matriculas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asistencias');
    }
};
