<?php

use App\Articulo;
use App\Categoria;
use App\Persona;
use App\Proveedor;
use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $persona = new Persona();
        $persona->nombre = 'Héctor Renato de la Cruz Ojeda';
        $persona->numero_documento = '7935317-9';
        $persona->direccion = 'Chiquimulilla, Santa Rosa';
        $persona->telefono = '57101225';
        $persona->email = 'renato.ojeda.1993@gmail.com';
        $persona->save();

        $user = new User();
        $user->id_persona = $persona->id;
        $user->usuario = 'renato';
        $user->password = bcrypt($user->usuario);
        $user->id_rol = 1;
        $user->save();

        echo 'Usuario --> '.$user->usuario.' || Password --> '.$user->usuario.PHP_EOL;

        $persona = new Persona();
        $persona->nombre = 'José Carlos Montepeque Hernández';
        $persona->numero_documento = '7935417-9';
        $persona->direccion = 'Chiquimulilla, Santa Rosa';
        $persona->telefono = '57102225';
        $persona->email = 'jmontepeque@gmail.com';
        $persona->save();

        $user = new User();
        $user->id_persona = $persona->id;
        $user->usuario = 'carlos';
        $user->password = bcrypt($user->usuario);
        $user->id_rol = 2;
        $user->save();

        echo 'Usuario --> '.$user->usuario.' || Password --> '.$user->usuario.PHP_EOL;

        $persona = new Persona();
        $persona->nombre = 'Marlon Samuel Hernández Flores';
        $persona->numero_documento = '7937317-9';
        $persona->direccion = 'Chiquimulilla, Santa Rosa';
        $persona->telefono = '57103225';
        $persona->email = 'marlonf@gmail.com';
        $persona->save();

        $user = new User();
        $user->id_persona = $persona->id;
        $user->usuario = 'marlon';
        $user->password = bcrypt($user->usuario);
        $user->id_rol = 3;
        $user->save();

        echo 'Usuario --> '.$user->usuario.' || Password --> '.$user->usuario.PHP_EOL;


        factory(Categoria::class, 50)->create();
        factory(Articulo::class, 200)->create();
        factory(Persona::class, 100)->create();
        factory(Proveedor::class, 200)->create();        
    }
}
