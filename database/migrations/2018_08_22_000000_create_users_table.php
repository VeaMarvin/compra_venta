<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {
    
    public function up() {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id_persona')->unsigned();
            $table->foreign('id_persona')->references('id')->on('personas')->onDelete('cascade')->onUpdate('cascade');
            $table->string('usuario')->unique();
            $table->string('password');
            $table->boolean('estado')->default(1);
            $table->integer('id_rol')->unsigned();
            $table->foreign('id_rol')->references('id')->on('roles')->onUpdate('cascade');
            $table->rememberToken();
            //$table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('users');
    }
}
