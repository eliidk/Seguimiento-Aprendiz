<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadsTable extends Migration
{
    public function up()
    {
        Schema::create('actividads', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->date('fecha_entrega');
            $table->text('criterios_valoracion');
            $table->unsignedBigInteger('ficha_id')->nullable(); // Si quieres asociar a una ficha
            $table->timestamps();

            $table->foreign('ficha_id')->references('id')->on('fichas')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('actividads');
    }
}