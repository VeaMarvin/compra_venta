<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepositosTable extends Migration
{
    public function up()
    {
        Schema::create('depositos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('boleta')->unique();
            $table->longText('foto');
            $table->date('fecha');
            $table->decimal('monto',10,2);
            $table->integer('id_empleado')->unsigned();
            $table->foreign('id_empleado')->references('id')->on('personas')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('depositos');
    }
}
