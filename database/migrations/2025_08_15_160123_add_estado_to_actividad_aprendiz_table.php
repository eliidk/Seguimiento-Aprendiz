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
        Schema::table('actividad_aprendizs', function ($table) {
            $table->enum('estado', ['Entregado', 'Tarde', 'No entregado'])->default('No entregado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actividad_aprendizs', function ($table) {
            $table->dropColumn('estado');
        });
    }
};
