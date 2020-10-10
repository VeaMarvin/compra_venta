<?php

namespace App\Imports;

use App\Articulo;
use App\Categoria;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductoImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $value) {
            if(!is_null($value[0])) {
                $categoria = Categoria::firstOrCreate(['nombre' => mb_strtoupper($value['0']), 'descripcion' => 'N/A']);

                if(is_null(Articulo::where('nombre', mb_strtoupper($value['2']))->first())) {
                    $articulo = new Articulo();
                    $articulo->id_categoria = $categoria->id;
                    $articulo->codigo = 0;
                    $articulo->nombre = mb_strtoupper($value['2']);
                    $articulo->precio_venta = !is_null($value['5']) ? $value['5'] : 0;
                    $articulo->stock = $value['4'];
                    $articulo->descripcion = mb_strtoupper($value['3']);
                    $articulo->save();

                    $articulo->codigo = $articulo->id;
                    $articulo->save();

                    echo 'Articulo --> ' . $articulo->nombre . PHP_EOL;
                }              
            }
        }
    }
}
