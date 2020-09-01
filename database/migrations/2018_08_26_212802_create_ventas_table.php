<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVentasTable extends Migration {
    public function up() {
        Schema::create('ventas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_cliente')->unsigned();
            $table->integer('id_usuario')->unsigned();
            $table->integer('id_talonario')->unsigned();
            $table->string('tipo_comprobante', 20);
            $table->string('serie_comprobante');
            $table->integer('numero_comprobante');
            $table->dateTime('fecha_hora');
            $table->decimal('impuesto', 4, 2);
            $table->decimal('total', 11, 2);
            $table->decimal('descuento',11,2);
            $table->string('estado', 20);
            $table->timestamps();
            $table->foreign('id_cliente')->references('id')->on('personas')->onUpdate('cascade');
            $table->foreign('id_usuario')->references('id_persona')->on('users')->onUpdate('cascade');
            $table->foreign('id_talonario')->references('id')->on('talonarios')->onUpdate('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('ventas');
    }
}
