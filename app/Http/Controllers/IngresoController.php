<?php

namespace App\Http\Controllers;
use App\User;
use App\Ingreso;
use App\Persona;
use App\Articulo;
use App\Proveedor;
use Carbon\Carbon;
use App\DetalleIngreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngresoController extends Controller {
 
    public function index(Request $request) {

        //if(!$request->ajax()) return redirect( '/' );

        $criterio = $request->criterio;
        $buscar = $request->buscar;
        if($buscar == '') {
            $ingresos = Ingreso::join('personas', 'ingresos.id_proveedor', '=', 'personas.id')
                ->join('users', 'ingresos.id_usuario', '=', 'users.id_persona')
                ->join('proveedores', 'ingresos.id_proveedor', '=', 'proveedores.id_persona')
                ->select(   'ingresos.id','ingresos.tipo_comprobante','ingresos.numero_comprobante',
                            'ingresos.serie_comprobante','ingresos.impuesto',
                            DB::raw('DATE_FORMAT(ingresos.fecha_hora, "%d-%m-%Y") as fecha_hora'),
                            'ingresos.total','ingresos.estado',
                            'personas.id as proveedor_id', 'personas.nombre', 'proveedores.contacto', 'users.usuario',
                            'ingresos.created_at' )
                ->orderBy('ingresos.id', 'desc')->paginate(10);
        }
        else{
            if($criterio == 'fecha_hora'){
                $ingresos = Ingreso::join('personas', 'ingresos.id_proveedor', '=', 'personas.id')
                    ->join('users', 'ingresos.id_usuario', '=', 'users.id_persona')
                    ->join('proveedores', 'ingresos.id_proveedor', '=', 'proveedores.id_persona')
                    ->select(   'ingresos.id','ingresos.tipo_comprobante','ingresos.numero_comprobante',
                                'ingresos.serie_comprobante','ingresos.impuesto',
                                DB::raw('DATE_FORMAT(ingresos.fecha_hora, "%d-%m-%Y") as fecha_hora'),
                                'ingresos.total','ingresos.estado',
                                'personas.id as proveedor_id', 'personas.nombre', 'proveedores.contacto', 'users.usuario',
                                'ingresos.created_at' )
                    ->whereBetween('ingresos.' . $criterio, [$buscar, date('Y-m-d')])
                    ->orderBy('ingresos.fecha_hora', 'desc')->paginate(10);
            }
            else {
                $ingresos = Ingreso::join('personas', 'ingresos.id_proveedor', '=', 'personas.id')
                ->join('users', 'ingresos.id_usuario', '=', 'users.id_persona')
                ->join('proveedores', 'ingresos.id_proveedor', '=', 'proveedores.id_persona')
                ->select(   'ingresos.id','ingresos.tipo_comprobante','ingresos.numero_comprobante',
                            'ingresos.serie_comprobante','ingresos.impuesto',
                            DB::raw('DATE_FORMAT(ingresos.fecha_hora, "%d-%m-%Y") as fecha_hora'),
                            'ingresos.total','ingresos.estado',
                            'personas.id as proveedor_id', 'personas.nombre', 'proveedores.contacto', 'users.usuario',
                            'ingresos.created_at' )
                ->where('ingresos.' . $criterio, 'like', '%' . $buscar . '%')
                ->orderBy('ingresos.id', 'desc')->paginate(10);
            }
        }
        return [
            'paginacion' => [
                'total' => $ingresos->total(),
                'pagina_actual' => $ingresos->currentPage(),
                'por_pagina' => $ingresos->perPage(),
                'ultima_pagina' => $ingresos->lastPage(),
                'desde' => $ingresos->firstItem(),
                'hasta' => $ingresos->lastItem()
            ],
            'ingresos' => $ingresos
        ];
    }

    public function getIngresosPDF(Request $request) {
        
        $criterio = $request->criterio;
        $buscar = $request->buscar;

        $ingresos = Ingreso::join('personas', 'ingresos.id_proveedor', '=', 'personas.id')
        ->join('users', 'ingresos.id_usuario', '=', 'users.id_persona')
        ->join('proveedores', 'ingresos.id_proveedor', '=', 'proveedores.id_persona')
        ->select(   'ingresos.id','ingresos.tipo_comprobante','ingresos.numero_comprobante',
                    'ingresos.serie_comprobante','ingresos.impuesto',
                    DB::raw('DATE_FORMAT(ingresos.fecha_hora, "%d-%m-%Y") as fecha_hora'),
                    'ingresos.total','ingresos.estado',
                    'personas.id as proveedor_id', 'personas.nombre', 'proveedores.contacto', 'users.usuario',
                    'ingresos.created_at' )
        ->whereBetween('ingresos.' . $criterio, [$buscar, date('Y-m-d')])
        ->orderBy('ingresos.fecha_hora', 'desc')->get();

        $cantidad = count($ingresos);
        $total = DB::table('ingresos')->whereBetween('ingresos.' . $criterio, [$buscar, date('Y-m-d')])->sum('ingresos.total');
        $pdf = \PDF::loadView( 'pdf.compras_pdf', ['ingresos'=>$ingresos, 'cantidad'=>$cantidad, 'total'=>$total] );
        return $pdf->download( 'compras.pdf' );        

    }

    public function create() {}

    public function store(Request $request) {
        if($request->nit != '' && $request->nit != 'CF')
            $this->validaciones($request,0);
        else
            $this->validaciones($request,0,true);
            
        try{
            DB::beginTransaction();

                if($request->id_proveedor == 0 || $request->id_proveedor == '')
                {
                    $persona = new Persona();
                    $persona->nombre = $request->proveedor;
                    $persona->tipo_documento = Persona::TIPO_DOCUMENTO;
                    $persona->numero_documento = $request->nit;
                    $persona->direccion = $request->direccion;
                    $persona->save();
    
                    $proveedor = new Proveedor();
                    $proveedor->id_persona = $persona->id;
                    $proveedor->contacto = $request->contacto;
                    $proveedor->telefono_contacto = $request->telefono_contacto;
                    $proveedor->save();

                    $request->id_proveedor = $persona->id;
                }

                $existe_ingreso = Ingreso::where('serie_comprobante',$request->serie_comprobante)->where('numero_comprobante',$request->numero_comprobante)->where('id_proveedor',$request->id_proveedor)->where('estado','!=',Ingreso::ESTADO_ANULADO)->first();

                if(!is_null($existe_ingreso))
                    return response()->json(['Ya existe la factura con estos datos' => ''],409);

                $time = Carbon::now('America/Guatemala');
                $ingreso = new Ingreso();
                $ingreso->id_proveedor = $request->id_proveedor;
                $ingreso->id_usuario = \Auth::user()->id_persona;
                $ingreso->tipo_comprobante = Ingreso::COMPROBANTE;
                $ingreso->serie_comprobante = $request->serie_comprobante;
                $ingreso->numero_comprobante = $request->numero_comprobante;
                $ingreso->fecha_hora = $request->fecha;
                $ingreso->impuesto = Ingreso::IMPUESTO;
                $ingreso->total = $request->total;
                $ingreso->estado = Ingreso::ESTADO_REGISTRADO;
                $ingreso->save();

                $detalles = $request->data;      // lista de detalles
                for($i=0; $i<count($detalles); $i++) {
                    $articulo = Articulo::findOrFail( $detalles[$i]['id_articulo'] );
                    $articulo->stock = $articulo->stock + $detalles[$i]['cantidad'];
                    $articulo->estado = '1';
                    $articulo->save();


                    $detalle = new DetalleIngreso();
                    $detalle->id_ingreso = $ingreso->id;
                    $detalle->id_articulo = $detalles[$i]['id_articulo'];
                    $detalle->cantidad = $detalles[$i]['cantidad'];
                    $detalle->precio = $detalles[$i]['precio'];
                    $detalle->save();
                }
               /* $fecha_actual = date( 'Y-m-d' );
                $numero_ventas = DB::table('ventas')->whereDate( 'created_at', $fecha_actual )->count();
                $numero_ingresos = DB::table('ingresos')->whereDate( 'created_at', $fecha_actual )->count();
                $datos = [
                    'ventas' => [
                        'numero'=> $numero_ventas,
                        'msj'=> 'Ventas'
                    ],
                    'ingresos' => [
                        'numero'=> $numero_ingresos,
                        'msj'=> 'Ingresos'
                    ],
                ];
                $all_users = User::all();
                foreach( $all_users as $user ) {
                    User::findOrFail( $user->id_persona )->notify( new NotifyAdmin( $datos ) );
                }*/
            DB::commit();
            return response()->json(['data'=>$ingreso],200); 
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json(['Sistema'=> $e->getMessage()],409);
        }
    }
    
    public function show($id) {
        
    }

    public function edit($id) {
        
    }

    public function update(Request $request, $id) {
        
    }
    
    public function destroy($id) {
        
    }

    public function darBajaEstado(Request $request) {
        $descontar = true;

        try{
            DB::beginTransaction();
                $ingreso = Ingreso::findOrFail( $request->id );

                $detalles = DetalleIngreso::where('id_ingreso', $ingreso->id)->get();      // lista de detalles
                foreach ($detalles as $key => $value) {
                    $articulo = Articulo::findOrFail( $value->id_articulo );
                    $articulo->stock = $articulo->stock - $value->cantidad;
                    if($articulo->stock < 0)
                    {                      
                        $descontar = false; 
                        break;
                    }
                }

                if($descontar == true)
                {
                    foreach ($detalles as $key => $value) {
                        $articulo = Articulo::findOrFail( $value->id_articulo );
                        $articulo->stock = $articulo->stock - $value->cantidad;
                        if($articulo->stock == 0)
                        {       
                            $articulo->save();               
                            $ingreso->estado = Ingreso::ESTADO_ANULADO;
                            $ingreso->save();
                        }
                        else
                        {
                            $ingreso->estado = Ingreso::ESTADO_ANULADO;
                            $ingreso->save();                            
                            $articulo->save(); 
                        }
                    }  
                }

            DB::commit();
            return response()->json(['data'=>$ingreso],200); 
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json(['Sistema'=> 'Ocurrio un error'],409);
        }
    }

    public function getCabecera(Request $request){
        $id = $request->id;
        $cabecera = Ingreso::join('personas', 'ingresos.id_proveedor', '=', 'personas.id')
            ->join('users', 'ingresos.id_usuario', '=', 'users.id_persona')
            ->select(   'ingresos.id','ingresos.tipo_comprobante','ingresos.numero_comprobante',
                        'ingresos.serie_comprobante','ingresos.fecha_hora','ingresos.impuesto',
                        'ingresos.total','ingresos.estado', 'personas.nombre', 'users.usuario' )
            ->where('ingresos.id','=',$id)
            ->orderBy('ingresos.id', 'desc')->take(1)->get();
        return [ 'cabecera' => $cabecera ];
    }
    public function getDetalles(Request $request){
        $id = $request->id;
        $detalles = DetalleIngreso::join('articulos', 'detalle_ingresos.id_articulo', '=', 'articulos.id')
            ->select(   'detalle_ingresos.id', 'detalle_ingresos.cantidad','detalle_ingresos.precio',
            'articulos.id', 'articulos.nombre', 'articulos.codigo' )
            ->where('detalle_ingresos.id_ingreso','=',$id)
            ->orderBy('detalle_ingresos.id', 'desc')->get();
        return [ 'detalles' => $detalles ];
    }

    public function validaciones(Request $request, $id, $cf=false){
        $messages = [
            'id_proveedor.required' => 'El proveedor es obligatoria',
            'id_proveedor.integer' => 'El proveedor tiene que ser un ID',
            'id_proveedor.exists' => 'El proveedor seleccionada no existe en la base de datos',
            'serie_comprobante.required' => 'El número de serie es obligatoria',
            'numero_comprobante.required' => 'El número de factura es obligatoria',
            'fecha.required' => 'La fecha de factura es obligatoria',
            'total.required' => 'El total de la factura es obligatoria',
            'total.numeric' => 'El total de la factura no puede contener letras',
            'data.id_articulo.required' => 'El articulo es obligatoria',
            'data.id_articulo.integer' => 'El articulo tiene que ser un ID',
            'data.id_articulo.exists' => 'El articulo seleccionada no existe en la base de datos',
            'data.cantidad.required' => 'La cantidad del detalle de la factura es obligatoria',
            'data.cantidad.numeric' => 'La cantidad del detalle de la factura no puede contener letras',
            'data.precio.required' => 'El precio del detalle de la factura es obligatoria',
            'data.precio.numeric' => 'El precio del detalle de la factura no puede contener letras',

            'proveedor.required' => 'El nombre del proveedor es obligatorio',
            'proveedor.unique' => 'El nombre del proveedor ya existe',
            'nit.required' => 'El NIT del proveedor es obligatorio',
            'nit.unique' => 'El NIT del proveedor ya existe',

            'telefono.digits_between' => 'El número de teléfono debe de tener 8 digitos',
            'telefono_contacto.digits_between' => 'El número de teléfono debe de tener 8 digitos',            
        ];

        if($request->id_proveedor == 0 || $request->id_proveedor == '')
        {
            if($cf == false)
            {
                $rules = [
                    'proveedor' => 'required|max:110|unique:personas,nombre',
                    'nit' => 'required|unique:personas,numero_documento',
                    'direccion' => 'max:250|nullable',
                    'contacto' => 'max:250|nullable',
                    'telefono_contacto' => 'digits_between:8,8|nullable',

                    'serie_comprobante' => 'required|max:15',
                    'numero_comprobante' => 'required|max:25',
                    'fecha' => 'required',
                    'total' => 'required|numeric',
                    'data.id_articulo.*' => 'required|integer|exists:articulos,id',
                    'data.cantidad.*' => 'required|numeric',
                    'data.precio.*' => 'required|numeric',                    
                ];
            }
            else
            {
                $rules = [
                    'proveedor' => 'required|max:110|unique:personas,nombre',
                    'direccion' => 'max:250|nullable',
                    'contacto' => 'max:250|nullable',
                    'telefono_contacto' => 'digits_between:8,8|nullable',

                    'serie_comprobante' => 'required|max:15',
                    'numero_comprobante' => 'required|max:25',
                    'fecha' => 'required',
                    'total' => 'required|numeric',
                    'data.id_articulo.*' => 'required|integer|exists:articulos,id',
                    'data.cantidad.*' => 'required|numeric',
                    'data.precio.*' => 'required|numeric',                    
                ]; 
            }
        }
        else
        {
            $rules = [
                'id_proveedor' => 'required|integer|exists:proveedores,id_persona',
                'serie_comprobante' => 'required|max:15',
                'numero_comprobante' => 'required|max:25',
                'fecha' => 'required',
                'total' => 'required|numeric',
                'data.id_articulo.*' => 'required|integer|exists:articulos,id',
                'data.cantidad.*' => 'required|numeric',
                'data.precio.*' => 'required|numeric',
            ];
        }

        $validator = $request->validate($rules,$messages);

        return $validator;
    }    
}
