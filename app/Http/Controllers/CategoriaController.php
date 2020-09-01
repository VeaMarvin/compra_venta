<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Categoria;
use App\Http\Requests\CrearCategoriaRequest;
use App\Http\Requests\ActualizarCategoriaRequest;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller {
    
    public function index(Request $request){
        /*if(!$request->ajax()) {
            return redirect( '/' );
        }*/

        $criterio = $request->criterio;
        $buscar = $request->buscar;

        if($buscar == '') {
            $categorias = Categoria::orderBy('id', 'desc')
                ->paginate(10);                   // eloquent
        }
        else{
            $categorias = Categoria::where($criterio, 'like', '%' . $buscar . '%')
                ->orderBy('id', 'desc')
                ->paginate(10);
        }

        //$categorias = DB::table('categorias')->paginate(5);     // query builder
        

        return [
            'paginacion' => [
                'total' => $categorias->total(),
                'pagina_actual' => $categorias->currentPage(),
                'por_pagina' => $categorias->perPage(),
                'ultima_pagina' => $categorias->lastPage(),
                'desde' => $categorias->firstItem(),
                'hasta' => $categorias->lastItem()
            ],
            'categorias' => $categorias
        ];
    }

    public function listaCategorias(Request $request) {
        $categorias = Categoria::where('estado', '=', '1')
            ->select('id', 'nombre')->orderBy('nombre', 'asc')->get();
        return [
            'categorias' => $categorias
        ];
    }

    public function create() {
        //
    }

    public function store(Request $request) {
        $this->validaciones($request,0);
        try {
            $categoria = new Categoria();
            $categoria->nombre = $request->nombre;
            $categoria->descripcion = $request->descripcion;           
            $categoria->save();
    
            return response()->json(['data'=>$categoria],200); 
        } catch (\Exception $e) {
            return response()->json(['Sistema'=> 'Ocurrio un error'],409);
        }
    }

    public function show($id) {
        //
    }

    public function edit($id) {
        //
    }

    public function update(Request $request) {
        $this->validaciones($request,$request->id);
        try {
            $categoria = Categoria::findOrFail( $request->id );
            $categoria->nombre = $request->nombre;
            $categoria->descripcion = $request->descripcion;           
            $categoria->save();
    
            return response()->json(['data'=>$categoria],200); 
        } catch (\Exception $e) {
            return response()->json(['Sistema'=> 'Ocurrio un error'],409);
        }
    }

    public function destroy($id) {
        //
    }

    public function darBajaEstado(Request $request) {
        $categoria = Categoria::findOrFail( $request->id );
        $categoria->estado = '0';
        $categoria->save();
    }

    public function darAltaEstado(Request $request) {
        $categoria = Categoria::findOrFail( $request->id );
        $categoria->estado = '1';
        $categoria->save();
    }

    
    public function validaciones(Request $request, $id){

        $messages = [
            'nombre.required' => 'El nombre de la categoría es obligatorio',
            'nombre.unique' => 'El nombre de la categoría ya existe',
            'descripcion.required' => 'La descripción de la categoría es obligatoria',
        ];

        if($id > 0){
            $rules = [
                'nombre' => 'required|max:50|unique:categorias,nombre,'.$id,
                'descripcion' => 'required|max:500'
            ];
        }
        else{
            $rules = [
                'nombre' => 'required|max:50|unique:categorias,nombre',
                'descripcion' => 'required|max:500'
            ];
        }

        $validator = $request->validate($rules, $messages);

        return $validator;
    }    
}
