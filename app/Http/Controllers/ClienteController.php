<?php

namespace App\Http\Controllers;
use App\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller {
    public function index(Request $request) {
        $criterio = $request->criterio;
        $buscar = $request->buscar;
        if($buscar == '') 
            $personas = Persona::orderBy('id', 'desc') ->paginate(10);
        else{
            $personas = Persona::where($criterio, 'like', '%' . $buscar . '%')
                ->orderBy('id', 'desc')
                ->paginate(10);
        }
        return [
            'paginacion' => [
                'total' => $personas->total(),
                'pagina_actual' => $personas->currentPage(),
                'por_pagina' => $personas->perPage(),
                'ultima_pagina' => $personas->lastPage(),
                'desde' => $personas->firstItem(),
                'hasta' => $personas->lastItem()
            ],
            'personas' => $personas
        ];
    }
    public function create() {
        
    }
    public function store(Request $request) {
        if($request->numero_documento != '' && $request->numero_documento != 'CF')
            $this->validaciones($request,0);
        else
            $this->validaciones($request,0,true);

        try {
            $persona = new Persona();
            $persona->nombre = $request->nombre;
            $persona->tipo_documento = Persona::TIPO_DOCUMENTO;
            $persona->numero_documento = $request->numero_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->save();
    
            return response()->json(['data'=>$persona],200); 
        } catch (\Exception $e) {
            return response()->json(['Sistema'=> 'Ocurrio un error'],409);
        }
    }
    public function show($id) {
        
    }
    public function edit($id) {
        
    }
    public function update(Request $request) {
        $this->validaciones($request,$request->id);
        try {
            $persona = Persona::findOrFail( $request->id );
            $persona->nombre = $request->nombre;
            $persona->numero_documento = $request->numero_documento;
            $persona->email = $request->email;
            $persona->telefono = $request->telefono;
            $persona->direccion = $request->direccion;
            $persona->save();
    
            return response()->json(['data'=>$persona],200); 
        } catch (\Exception $e) {
            return response()->json(['Sistema'=> 'Ocurrio un error'],409);
        }
    }
    public function destroy($id) {
        
    }

    public function getClientes(Request $request) {
        //if(!$request->ajax()) return redirect( '/' );
        $nit = '';
        $cliente = '';

        if(isset($request->nit))
            $nit = $request->nit;

        if(isset($request->cliente))
            $cliente = $request->cliente;

        $clientes = Persona::where('nombre', '=',$cliente)
            ->orWhere('numero_documento', '=',$nit)
            ->select('id',DB::raw('CONCAT ("NIT ",personas.numero_documento," -- CLIENTE ",personas.nombre) AS nombre'),
            'numero_documento as nit','personas.nombre AS cliente','personas.direccion AS direccion')->get();
        return [ 'clientes' => $clientes ];
    }

    public function validaciones(Request $request, $id, $cf=false){
        $messages = [
            'nombre.required' => 'El nombre del empleado es obligatorio',
            'nombre.unique' => 'El nombre del empleado ya existe',
            'numero_documento.required' => 'El NIT del empleado es obligatorio',
            'numero_documento.unique' => 'El NIT del empleado ya existe',

            'telefono.digits_between' => 'El número de teléfono debe de tener 8 digitos',
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
                ];
            }
            else
            {
                $rules = [
                    'nombre' => 'required|max:110',
                    'direccion' => 'max:250|nullable',
                    'telefono' => 'digits_between:8,8|nullable',
                    'email' => 'email|nullable',
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
                ];
            }
            else
            {
                $rules = [
                    'nombre' => 'required|max:110|unique:articulos,nombre',
                    'direccion' => 'max:250|nullable',
                    'telefono' => 'digits_between:8,8|nullable',
                    'email' => 'email|nullable',
                ]; 
            }
        }

        $validator = $request->validate($rules,$messages);

        return $validator;
    }     
}
