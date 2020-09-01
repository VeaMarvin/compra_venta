<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Persona;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller { 

    public function index(Request $request) {
        $criterio = $request->criterio;
        $buscar = $request->buscar;
        if($buscar == '') {
            $users = User::join('personas', 'users.id_persona', '=', 'personas.id')
                ->join('roles', 'users.id_rol', '=', 'roles.id')
                ->select(   'personas.id', 'personas.nombre', 'personas.tipo_documento',
                            'personas.numero_documento', 'personas.email',
                            'personas.direccion', 'personas.telefono', 'users.usuario',
                            'users.password', 'users.estado', 'roles.id as id_rol', 'roles.nombre as rol',
                            'roles.descripcion' )
                ->orderBy('personas.id', 'desc')->paginate(10);
        }
        else{
            if($criterio == 'usuario'){
                $users = User::join('personas', 'users.id_persona', '=', 'personas.id')
                    ->join('roles', 'users.id_rol', '=', 'roles.id')
                    ->select(   'personas.id', 'personas.nombre', 'personas.tipo_documento',
                                'personas.numero_documento', 'personas.email',
                                'personas.direccion', 'personas.telefono', 'users.usuario',
                                'users.password', 'users.estado', 'roles.id as id_rol', 'roles.nombre as rol',
                                'roles.descripcion' )
                    ->where('users.' . $criterio, 'like', '%' . $buscar . '%')
                    ->orderBy('personas.id', 'desc')->paginate(10);
            }
            else{
                $users = User::join('personas', 'users.id_persona', '=', 'personas.id')
                    ->join('roles', 'users.id_rol', '=', 'roles.id')
                    ->select(   'personas.id', 'personas.nombre', 'personas.tipo_documento',
                                'personas.numero_documento', 'personas.email',
                                'personas.direccion', 'personas.telefono', 'users.usuario',
                                'users.password', 'users.estado', 'roles.id as id_rol', 'roles.nombre as rol',
                                'roles.descripcion' )
                    ->where('personas.' . $criterio, 'like', '%' . $buscar . '%')
                    ->orderBy('personas.id', 'desc')->paginate(10);
            }
        }
        return [
            'paginacion' => [
                'total' => $users->total(),
                'pagina_actual' => $users->currentPage(),
                'por_pagina' => $users->perPage(),
                'ultima_pagina' => $users->lastPage(),
                'desde' => $users->firstItem(),
                'hasta' => $users->lastItem()
            ],
            'users' => $users
        ];
    }

    public function getUsuarios(Request $request) {
        //if(!$request->ajax()) return redirect( '/' );
        $filtro = $request->filtro;
        $criterio = 'nombre';
        $users = User::join('personas', 'users.id_persona', '=', 'personas.id')
        ->join('roles', 'users.id_rol', '=', 'roles.id')
        ->select(   'personas.id', 'personas.nombre', 'personas.tipo_documento',
                    'personas.numero_documento', 'personas.email',
                    'personas.direccion', 'personas.telefono', 'users.usuario',
                    'users.password', 'users.estado', 'roles.id as id_rol', 'roles.nombre as rol',
                    'roles.descripcion' )
        ->where('personas.' . $criterio, 'like', '%' . $filtro . '%')
        ->where('users.id_persona','!=',Auth::user()->id_persona)
        ->where('roles.id','!=',3)
        ->orderBy('personas.id', 'desc')->get();
        return [ 'users' => $users ];
    }

    public function create() {
        
    }
    public function store(Request $request) {
        if($request->numero_documento != '' && $request->numero_documento != 'CF')
            $this->validaciones($request,0);
        else
            $this->validaciones($request,0,true);
    
        try {
            DB::beginTransaction();
            $persona = new Persona();
            $persona->nombre = $request->nombre;
            $persona->tipo_documento = Persona::TIPO_DOCUMENTO;
            $persona->numero_documento = $request->numero_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->save();    
            $user = new User();
            $user->id_persona = $persona->id;
            $user->usuario = $request->usuario;
            $user->password = bcrypt($request->password);
            $user->estado = '1';
            $user->id_rol = $request->id_rol;
            $user->save();
            DB::commit();
    
            return response()->json(['data'=>$user],200); 
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['Sistema'=> 'Ocurrio un error'],409);
        }
    }
    public function show($id) {
        
    }
    public function edit($id) {
        
    }
    public function update(Request $request, $id) {
        // $this->validaciones($request,$request->id);
        try {
            DB::beginTransaction();
            $persona = Persona::findOrFail($request->id);
            $user = User::findOrFail($request->id);
            $persona->nombre = $request->nombre;
            $persona->numero_documento = $request->numero_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->save();    
            $user->usuario = $request->usuario;
            $user->password = bcrypt($request->password);
            $user->estado = '1';
            $user->id_rol = $request->id_rol;
            $user->save();
            DB::commit();
    
            return response()->json(['data'=>$user],200); 
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['Sistema'=> 'Ocurrio un error'],409);
        }
    }
    public function destroy($id) {
        
    }
    public function darBajaEstado(Request $request) {
        $user = User::findOrFail( $request->id );
        $user->estado = '0';
        $user->save();
    }

    public function darAltaEstado(Request $request) {
        $user = User::findOrFail( $request->id );
        $user->estado = '1';
        $user->save();
    }

    public function validaciones(Request $request, $id, $cf=false){
        $messages = [
            'nombre.required' => 'El nombre del empleado es obligatorio',
            'nombre.unique' => 'El nombre del empleado ya existe',
            'numero_documento.required' => 'El NIT del empleado es obligatorio',
            'numero_documento.unique' => 'El NIT del empleado ya existe',

            'telefono.digits_between' => 'El número de teléfono debe de tener 8 digitos',

            'usuario.required' => 'El usuario del empleado es obligatorio',
            'usuario.unique' => 'El usuario del empleado ya existe',
        ];

        if($id > 0){
            if($cf == false)
            {
                $rules = [
                    'nombre' => 'required|max:110|unique:personas,numero_documento',
                    'numero_documento' => 'required|unique:personas,numero_documento,'.$id,
                    'direccion' => 'max:250|nullable',
                    'telefono' => 'digits_between:8,8|nullable',
                    'email' => 'email|nullable',

                    'usuario' => 'required|max:25|unique:users,usuario',
                ];
            }
            else
            {
                $rules = [
                    'nombre' => 'required|max:110',
                    'direccion' => 'max:250|nullable',
                    'telefono' => 'digits_between:8,8|nullable',
                    'email' => 'email|nullable',

                    'usuario' => 'required|max:25|unique:users,usuario',
                ];
            }                
        }
        else{
            if($cf == false)
            {
                $rules = [
                    'nombre' => 'required|max:110|unique:articulos,nombre',
                    'numero_documento' => 'required|unique:personas,numero_documento',
                    'direccion' => 'max:250|nullable',
                    'telefono' => 'digits_between:8,8|nullable',
                    'email' => 'email|nullable',

                    'usuario' => 'required|max:25|unique:users,usuario',
                ];
            }
            else
            {
                $rules = [
                    'nombre' => 'required|max:110|unique:articulos,nombre',
                    'direccion' => 'max:250|nullable',
                    'telefono' => 'digits_between:8,8|nullable',
                    'email' => 'email|nullable',

                    'usuario' => 'required|max:25|unique:users,usuario',
                ]; 
            }
        }

        $validator = $request->validate($rules,$messages);

        return $validator;
    }    
}
