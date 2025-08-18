<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadAprendizsTable extends Migration
{
    public function up()
    {
        Schema::create('actividad_aprendizs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('actividad_id');
            $table->unsignedBigInteger('aprendiz_id');
            $table->timestamps();

            $table->foreign('actividad_id')->references('id')->on('actividads')->onDelete('cascade');
            $table->foreign('aprendiz_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('actividad_aprendizs');
    }
}