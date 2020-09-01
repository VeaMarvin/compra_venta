<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Proveedor;
use App\Persona;

class ProveedorController extends Controller {
    
    public function index(Request $request) {

        $criterio = $request->criterio;
        $buscar = $request->buscar;
        if($buscar == '') {
            $proveedores = Proveedor::join('personas', 'proveedores.id_persona', '=', 'personas.id')
                ->select(   'personas.id', 'personas.nombre', 'personas.tipo_documento',
                            'personas.numero_documento', 'personas.email',
                            'personas.direccion', 'personas.telefono', 'proveedores.contacto',
                            'proveedores.telefono_contacto' )
                ->orderBy('personas.id', 'desc')->paginate(10);
        }
        else{
            if($criterio == 'contacto'){    // para que busque en la tabla proveedores
                $proveedores = Proveedor::join('personas', 'proveedores.id_persona', '=', 'personas.id')
                ->select(   'personas.id', 'personas.nombre', 'personas.tipo_documento',
                            'personas.numero_documento', 'personas.email',
                            'personas.direccion', 'personas.telefono', 'proveedores.contacto',
                            'proveedores.telefono_contacto' )
                ->where('proveedores.contacto', 'like', '%' . $buscar . '%')
                ->orderBy('personas.id', 'desc')->paginate(10);
            }
            else{
                $proveedores = Proveedor::join('personas', 'proveedores.id_persona', '=', 'personas.id')
                ->select(   'personas.id', 'personas.nombre', 'personas.tipo_documento',
                            'personas.numero_documento', 'personas.email',
                            'personas.direccion', 'personas.telefono', 'proveedores.contacto',
                            'proveedores.telefono_contacto' )
                ->where('personas.' . $criterio, 'like', '%' . $buscar . '%')
                ->orderBy('personas.id', 'desc')->paginate(10);
            }
        }
        return [
            'paginacion' => [
                'total' => $proveedores->total(),
                'pagina_actual' => $proveedores->currentPage(),
                'por_pagina' => $proveedores->perPage(),
                'ultima_pagina' => $proveedores->lastPage(),
                'desde' => $proveedores->firstItem(),
                'hasta' => $proveedores->lastItem()
            ],
            'proveedores' => $proveedores
        ];
    }

    public function create() {
    
    }

    public function store(Request $request) {
        if($request->numero_documento != '' && $request->numero_documento != 'CF')
            $this->validaciones($request,0);
        else
            $this->validaciones($request,0,true);

        try{
            DB::beginTransaction();
            $persona = new Persona();
            $persona->nombre = $request->nombre;
            $persona->tipo_documento = Persona::TIPO_DOCUMENTO;
            $persona->numero_documento = $request->numero_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->save();    

            $proveedor = new Proveedor();
            $proveedor->id_persona = $persona->id;
            $proveedor->contacto = $request->contacto;
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->save();
            DB::commit();

            return response()->json(['data'=>$persona],200); 
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json(['Sistema'=> $e],409);
        }
    }

    public function show($id) {
        
    }

    public function edit($id) {
        
    }

    public function update(Request $request) {
        $this->validaciones($request,$request->id);
        try{
            DB::beginTransaction();
            $persona = Persona::findOrFail($request->id);
            $proveedor = Proveedor::findOrFail($request->id);
            $persona->nombre = $request->nombre;
            $persona->numero_documento = $request->numero_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->save();    
            $proveedor->contacto = $request->contacto;
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->save();
            DB::commit();

            return response()->json(['data'=>$persona],200); 
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json(['Sistema'=> 'Ocurrio un error'],409);
        }    
    }

    public function destroy($id) {
        
    }

    public function getProveedores(Request $request) {
        //if(!$request->ajax()) return redirect( '/' );
        $nit = '';
        $proveedor = '';

        if(isset($request->nit))
            $nit = $request->nit;

        if(isset($request->proveedor))
            $proveedor = $request->proveedor;

        $proveedores = Proveedor::join('personas', 'proveedores.id_persona', '=', 'personas.id')
            ->where('personas.nombre', '=', $proveedor )
            ->orWhere( 'personas.numero_documento', '=', $nit )
            ->select('personas.id', DB::raw('CONCAT ("NIT ",personas.numero_documento," -- PROVEEDOR ",personas.nombre) AS nombre'), 
            'proveedores.contacto', 'personas.nombre as proveedor', 'personas.numero_documento as nit', 
            'proveedores.telefono_contacto', 'personas.direccion')->get();
            
        return [ 'proveedores' => $proveedores ];
    }

    public function validaciones(Request $request, $id, $cf=false){
        $messages = [
            'nombre.required' => 'El nombre del proveedor es obligatorio',
            'nombre.unique' => 'El nombre del proveedor ya existe',
            'numero_documento.required' => 'El NIT del proveedor es obligatorio',
            'numero_documento.unique' => 'El NIT del proveedor ya existe',

            'telefono.digits_between' => 'El número de teléfono debe de tener 8 digitos',
            'telefono_contacto.digits_between' => 'El número de teléfono debe de tener 8 digitos',
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
                    'contacto' => 'max:250|nullable',
                    'telefono_contacto' => 'digits_between:8,8|nullable',
                ];
            }
            else
            {
                $rules = [
                    'nombre' => 'required|max:110',
                    'direccion' => 'max:250|nullable',
                    'telefono' => 'digits_between:8,8|nullable',
                    'email' => 'email|nullable',
                    'contacto' => 'max:250|nullable',
                    'telefono_contacto' => 'digits_between:8,8|nullable',
                ];
            }                
        }
        else{
            if($cf == false)
            {
                $rules = [
                    'nombre' => 'required|max:110|unique:personas,nombre',
                    'numero_documento' => 'required|unique:personas,numero_documento',
                    'direccion' => 'max:250|nullable',
                    'telefono' => 'digits_between:8,8|nullable',
                    'email' => 'email|nullable',
                    'contacto' => 'max:250|nullable',
                    'telefono_contacto' => 'digits_between:8,8|nullable',
                ];
            }
            else
            {
                $rules = [
                    'nombre' => 'required|max:110|unique:personas,nombre',
                    'direccion' => 'max:250|nullable',
                    'telefono' => 'digits_between:8,8|nullable',
                    'email' => 'email|nullable',
                    'contacto' => 'max:250|nullable',
                    'telefono_contacto' => 'digits_between:8,8|nullable',
                ]; 
            }
        }

        $validator = $request->validate($rules,$messages);

        return $validator;
    }
}
