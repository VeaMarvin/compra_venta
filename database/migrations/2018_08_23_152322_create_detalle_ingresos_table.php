<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleIngresosTable extends Migration {
    
    public function up() {
        Schema::create('detalle_ingresos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_ingreso')->unsigned();
            $table->integer('id_articulo')->unsigned();
            $table->integer('cantidad');
            $table->decimal('precio', 11, 2);
            $table->foreign('id_ingreso')->references('id')->on('ingresos')->onUpdate('cascade');
            $table->foreign('id_articulo')->references('id')->on('articulos')->onUpdate('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('detalle_ingresos');
    }
}
