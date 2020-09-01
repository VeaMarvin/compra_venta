<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIngresosTable extends Migration {
    
    public function up() {
        Schema::create('ingresos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_proveedor')->unsigned();
            $table->integer('id_usuario')->unsigned();
            $table->string('tipo_comprobante', 20);
            $table->string('serie_comprobante', 7)->nullable();
            $table->string('numero_comprobante', 10);
            $table->date('fecha_hora');
            $table->decimal('impuesto', 4, 2);
            $table->decimal('total', 11, 2);
            $table->string('estado', 20);
            $table->timestamps();
            $table->foreign('id_proveedor')->references('id_persona')->on('proveedores')->onUpdate('cascade');
            $table->foreign('id_usuario')->references('id_persona')->on('users')->onUpdate('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('ingresos');
    }
}
