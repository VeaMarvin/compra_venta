<?php

use App\User;
use App\Persona;
use App\Articulo;
use App\Categoria;
use App\Proveedor;
use App\Imports\ProductoImport;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $persona = new Persona();
        $persona->nombre = 'administrador';
        $persona->numero_documento = '000000-0';
        $persona->direccion = 'Chiquimulilla, Santa Rosa';
        $persona->telefono = '00000000';
        $persona->email = 'samboro@samboro.com';
        $persona->save();

        $user = new User();
        $user->id_persona = $persona->id;
        $user->usuario = 'administrador';
        $user->password = bcrypt($user->usuario);
        $user->id_rol = 1;
        $user->save();

        echo 'Usuario --> '.$user->usuario.' || Password --> '.$user->usuario.PHP_EOL;

        Excel::import(new ProductoImport, 'database/seeds/Catalogo/producto.xlsx');
    }
}
