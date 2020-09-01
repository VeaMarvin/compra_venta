<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTurnosTable extends Migration
{
    public function up()
    {
        Schema::create('turnos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_entrega')->unsigned();
            $table->integer('id_recibe')->unsigned();
            $table->decimal('caja_inicio',10,2);
            $table->decimal('caja_fin',10,2)->default(0);
            $table->dateTime('fecha_recibe')->nullable();
            $table->dateTime('fecha_entrega')->nullable();
            $table->string('tiempo_transcurrido')->default(0);
            $table->boolean('activo')->default(true);
            $table->string('cuadro')->nullable();

            $table->foreign('id_entrega')->references('id')->on('personas')->onUpdate('cascade');
            $table->foreign('id_recibe')->references('id')->on('personas')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('turnos');
    }
}
