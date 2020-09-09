<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditosTable extends Migration
{
    public function up()
    {
        Schema::create('creditos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_venta')->unisgned();
            $table->decimal('abono',10,2);
            $table->dateTime('fecha_hora');
            $table->foreign('id_venta')->references('id')->on('ventas')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('creditos');
    }
}
