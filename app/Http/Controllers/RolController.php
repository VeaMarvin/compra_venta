<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Rol;

class RolController extends Controller {
    
    public function index(Request $request) {
        $criterio = $request->criterio;
        $buscar = $request->buscar;

        if($buscar == '') {
            $roles = Rol::orderBy('id', 'desc')->paginate(10);
        }
        else{
            $roles = Rol::where($criterio, 'like', '%' . $buscar . '%')
                ->orderBy('id', 'desc')
                ->paginate(10);
        }
        return [
            'paginacion' => [
                'total' => $roles->total(),
                'pagina_actual' => $roles->currentPage(),
                'por_pagina' => $roles->perPage(),
                'ultima_pagina' => $roles->lastPage(),
                'desde' => $roles->firstItem(),
                'hasta' => $roles->lastItem()
            ],
            'roles' => $roles
        ];
    }

    public function listaRoles(Request $request) {
        $roles = Rol::where('estado', '=', '1')
            ->select('id', 'nombre')->orderBy('nombre', 'asc')->get();
        return [
            'roles' => $roles
        ];
    }

    public function create() {
        
    }

    public function store(Request $request) {
        $rol = new Rol();
        $rol->nombre = $request->nombre;
        $rol->descripcion = $request->descripcion;
        $rol->estado = '1';
        $rol->save();
    }

    public function show($id) {
        
    }

    public function edit($id) {
        
    }

    public function update(Request $request, $id) {
        $rol = Rol::findOrFail( $request->id );
        $rol->descripcion = $request->descripcion;
        $rol->save();
    }

    public function destroy($id) {
        
    }

    public function darBajaEstado(Request $request) {
        $rol = Rol::findOrFail( $request->id );
        $rol->estado = '0';
        $rol->save();
    }

    public function darAltaEstado(Request $request) {
        $rol = Rol::findOrFail( $request->id );
        $rol->estado = '1';
        $rol->save();
    }
}
