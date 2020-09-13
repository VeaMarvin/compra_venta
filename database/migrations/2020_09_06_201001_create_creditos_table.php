<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditosTable extends Migration
{
    public function up() {
        Schema::create('creditos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_venta')->unsigned();
            $table->dateTime('fecha_hora');
            $table->decimal('abono', 11, 2);
            $table->timestamps();
            $table->foreign('id_venta')->references('id')->on('ventas')->onUpdate('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('creditos');
    }
}
