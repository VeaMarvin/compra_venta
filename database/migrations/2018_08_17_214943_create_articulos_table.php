<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticulosTable extends Migration {
    
    public function up() {
        Schema::create('articulos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_categoria')->unsigned();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 100)->unique();
            $table->decimal('precio_venta', 10, 2);
            $table->decimal('stock', 10, 2);
            $table->string('descripcion', 256)->nullable();
            $table->boolean('estado')->default(1);
            $table->timestamps();
            $table->foreign('id_categoria')->references('id')->on('categorias')->onUpdate('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('articulos');
    }
}
