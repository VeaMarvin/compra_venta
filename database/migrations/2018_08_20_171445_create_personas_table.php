<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonasTable extends Migration {
    
    public function up() {
        Schema::create('personas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 125)->unique();
            $table->string('tipo_documento', 20)->default('NIT');
            $table->string('numero_documento', 20);
            $table->string('direccion', 125)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('personas');
    }
}
