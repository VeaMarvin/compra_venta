<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTalonariosTable extends Migration
{
    public function up()
    {
        Schema::create('talonarios', function (Blueprint $table) {
            $table->increments('id');
            $table->string('serie',2);
            $table->integer('inicio');
            $table->integer('fin');
            $table->boolean('activo')->default(true);
            $table->integer('descontar')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('talonarios');
    }
}
