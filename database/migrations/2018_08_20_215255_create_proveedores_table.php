<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProveedoresTable extends Migration {
    public function up() {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->integer('id_persona')->unsigned();
            $table->foreign('id_persona')->references('id')->on('personas')->onDelete('cascade')->onUpdate('cascade');
            $table->string('contacto', 100)->nullable();
            $table->string('telefono_contacto', 8)->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('proveedores');
    }
}

