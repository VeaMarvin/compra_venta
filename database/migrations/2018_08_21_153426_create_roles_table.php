<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration {
    
    public function up() {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 30)->unique();
            $table->string('descripcion', 100)->nullable();
            $table->boolean('estado')->default(1);
        });

        DB::table('roles')->insert(array('id'=>1, 'nombre'=>'Administrador', 'descripcion'=>'Administrador del sitio'));
        DB::table('roles')->insert(array('id'=>2, 'nombre'=>'Vendedor', 'descripcion'=>'Vendedor área venta'));
        DB::table('roles')->insert(array('id'=>3, 'nombre'=>'Almacenador', 'descripcion'=>'Almacenador área compras'));
    }

    public function down() {
        Schema::dropIfExists('roles');
    }
}
