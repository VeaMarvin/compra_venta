<?php

namespace App\Http\Controllers;
use App\User;
use App\Venta;
use App\Articulo;
use App\Talonario;
use Carbon\Carbon;
use App\DetalleVenta;
use Illuminate\Http\Request;
use App\Notifications\NotifyAdmin;
use App\Persona;
use App\Turno;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller {
    
    public function index(Request $request) {

        //if(!$request->ajax()) return redirect( '/' );

        $criterio = $request->criterio;
        $buscar = $request->buscar;
        if($buscar == '') {
            $ventas = Venta::join('personas', 'ventas.id_cliente', '=', 'personas.id')
                ->join('users', 'ventas.id_usuario', '=', 'users.id_persona')
                ->select(   'ventas.id','ventas.tipo_comprobante','ventas.numero_comprobante',
                            'ventas.serie_comprobante','ventas.impuesto',
                            DB::raw('DATE_FORMAT(ventas.fecha_hora, "%d-%m-%Y %H:%i:%s") as fecha_hora'),
                            'ventas.total','ventas.descuento','ventas.estado',
                            'personas.id as cliente_id', 'personas.nombre', 'users.usuario',
                            'ventas.created_at' )
                ->orderBy('ventas.id', 'desc')->paginate(10);
        }
        else{
            if($criterio == 'fecha_hora') {
                $ventas = Venta::join('personas', 'ventas.id_cliente', '=', 'personas.id')
                ->join('users', 'ventas.id_usuario', '=', 'users.id_persona')
                ->select(   'ventas.id','ventas.tipo_comprobante','ventas.numero_comprobante',
                            'ventas.serie_comprobante','ventas.impuesto',
                            DB::raw('DATE_FORMAT(ventas.fecha_hora, "%d-%m-%Y %H:%i:%s") as fecha_hora'),
                            'ventas.total','ventas.descuento','ventas.estado',
                            'personas.id as cliente_id', 'personas.nombre', 'users.usuario',
                            'ventas.created_at' )
                ->whereBetween(DB::raw('DATE_FORMAT(ventas.fecha_hora, "%Y-%m-%d")'), [$buscar, date('Y-m-d')])
                ->orderBy('ventas.fecha_hora', 'desc')->paginate(10);
            }
            else {
                $ventas = Venta::join('personas', 'ventas.id_cliente', '=', 'personas.id')
                ->join('users', 'ventas.id_usuario', '=', 'users.id_persona')
                ->select(   'ventas.id','ventas.tipo_comprobante','ventas.numero_comprobante',
                            'ventas.serie_comprobante','ventas.impuesto',
                            DB::raw('DATE_FORMAT(ventas.fecha_hora, "%d-%m-%Y %H:%i:%s") as fecha_hora'),
                            'ventas.total','ventas.descuento','ventas.estado',
                            'personas.id as cliente_id', 'personas.nombre', 'users.usuario',
                            'ventas.created_at' )
                ->where('ventas.' . $criterio, 'like', '%' . $buscar . '%')
                ->orderBy('ventas.id', 'desc')->paginate(10);
            }
        }
        return [
            'paginacion' => [
                'total' => $ventas->total(),
                'pagina_actual' => $ventas->currentPage(),
                'por_pagina' => $ventas->perPage(),
                'ultima_pagina' => $ventas->lastPage(),
                'desde' => $ventas->firstItem(),
                'hasta' => $ventas->lastItem()
            ],
            'ventas' => $ventas
        ];
    }

    public function getCabecera(Request $request){
        $id = $request->id;
        $cabecera = Venta::join('personas', 'ventas.id_cliente', '=', 'personas.id')
            ->join('users', 'ventas.id_usuario', '=', 'users.id_persona')
            ->select(   'ventas.id','ventas.tipo_comprobante','ventas.numero_comprobante',
                        'ventas.serie_comprobante','ventas.fecha_hora','ventas.impuesto',
                        'ventas.total','ventas.descuento','ventas.estado', 'personas.nombre', 'users.usuario',
                        'ventas.created_at' )
            ->where('ventas.id','=',$id)
            ->orderBy('ventas.id', 'desc')->take(1)->get();
        return [ 'cabecera' => $cabecera ];
    }

    public function getDetalles(Request $request){
        $id = $request->id;
        $detalles = DetalleVenta::join('articulos', 'detalle_ventas.id_articulo', '=', 'articulos.id')
            ->select(   'detalle_ventas.id', 'detalle_ventas.cantidad','detalle_ventas.precio', 
            'detalle_ventas.descuento', 'articulos.id', 'articulos.nombre', 'articulos.codigo' )
            ->where('detalle_ventas.id_venta','=',$id)
            ->orderBy('detalle_ventas.id', 'desc')->get();
        return [ 'detalles' => $detalles ];
    }

    public function getVentaListPDF(Request $request) {
        $criterio = $request->criterio;
        $buscar = $request->buscar;

        $ventas = Venta::join('personas', 'ventas.id_cliente', '=', 'personas.id')
        ->join('users', 'ventas.id_usuario', '=', 'users.id_persona')
        ->select(   'ventas.id','ventas.tipo_comprobante','ventas.numero_comprobante',
                    'ventas.serie_comprobante','ventas.impuesto',
                    DB::raw('DATE_FORMAT(ventas.fecha_hora, "%d-%m-%Y %H:%i:%s") as fecha_hora'),
                    'ventas.total','ventas.descuento','ventas.estado',
                    'personas.id as cliente_id', 'personas.nombre', 'users.usuario',
                    'ventas.created_at' )
        ->whereBetween(DB::raw('DATE_FORMAT(ventas.fecha_hora, "%Y-%m-%d")'), [$buscar, date('Y-m-d')])
        ->orderBy('ventas.fecha_hora', 'desc')->get();

        $cantidad = count($ventas);
        $total = DB::table('ventas')->whereBetween(DB::raw('DATE_FORMAT(ventas.fecha_hora, "%Y-%m-%d")'), [$buscar, date('Y-m-d')])->sum('ventas.total');
        $pdf = \PDF::loadView( 'pdf.ventas_pdf', ['ventas'=>$ventas, 'cantidad'=>$cantidad, 'total'=>$total] );
        return $pdf->download( 'ventas.pdf' ); 
    }

    public function create() {
        
    }

    public function store(Request $request) {
        if($request->nit != '' && $request->nit != 'CF')
            $this->validaciones($request,0);
        else
            $this->validaciones($request,0,true);

        try{
            DB::beginTransaction();

            /*$turno_activo = Turno::where('id_recibe',Auth::user()->id_persona)->where('activo',true)->first();

            if(is_null($turno_activo))
                return response()->json(['Actualmente usted no se encuentra en turno, por lo tanto no tiene permisos para realizar ventas.' => ''],409);
            */

            $evitar_negativo = true;

            $talonario_activo = Talonario::where('activo', 1)->first();
            
            if(is_null($talonario_activo))
                return response()->json(['No se encuentra ningun talonario activo' => ''],409);
            else{
                if(($request->tipo_comprobante=='FACTURA')
                &&($talonario_activo->descontar == 0)) {
                    $talonario_activo->descontar = $talonario_activo->inicio;
                    $talonario_activo->save();
                } else if(($request->tipo_comprobante=='FACTURA')
                &&($talonario_activo->descontar < $talonario_activo->fin+1)) {
                    $talonario_activo->descontar = $talonario_activo->descontar+1;
                    $talonario_activo->save();
                } else if($request->tipo_comprobante=='FACTURA'){
                    return response()->json(['Existe error en la configuración del talonario' => ''],409);
                }
            }
            

            if($request->id_cliente == 0 || $request->id_cliente == '')
            {
                $persona = new Persona();
                $persona->nombre = $request->cliente;
                $persona->tipo_documento = Persona::TIPO_DOCUMENTO;
                $persona->numero_documento = $request->nit;
                $persona->direccion = $request->direccion;
                $persona->save();

                $request->id_cliente = $persona->id;
            }

            $time = Carbon::now('America/Guatemala');
            $venta = new Venta();
            $venta->id_cliente = $request->id_cliente;
            $venta->id_usuario = \Auth::user()->id_persona;
            $venta->id_talonario = $talonario_activo->id;
            $descuento = 0;

            if($request->tipo_comprobante=='FACTURA'){
                $venta->serie_comprobante = $talonario_activo->serie;
                $venta->numero_comprobante = $talonario_activo->descontar;
                $venta->tipo_comprobante = 'FACTURA';
            } 
            else {
                $comprobante = 0;
                $recibo = Venta::where('tipo_comprobante', 'RECIBO')->get()->last();
                if(!is_null($recibo)) $comprobante = $recibo->numero_comprobante;
                $venta->serie_comprobante = 'Recibo';
                $venta->numero_comprobante = $comprobante+1;
                $venta->tipo_comprobante = 'RECIBO';
                $descuento = $request->descuento;
            }
            
            $venta->fecha_hora = $time->toDateTimeString();
            $venta->impuesto = Venta::IMPUESTO;
            $venta->descuento = $request->descuento;
            $venta->total = $request->total - $descuento;            
            $venta->estado = Venta::ESTADO_REGISTRADO;
            $venta->save();

            $detalles = $request->data;      // lista de detalles
            for($i=0; $i<count($detalles); $i++) {
                $articulo = Articulo::findOrFail( $detalles[$i]['id_articulo'] );
                $articulo->stock = $articulo->stock - $detalles[$i]['cantidad'];
                if($articulo->stock < 0)
                {                      
                    $evitar_negativo = false; 
                    break;
                }
            }

            if($evitar_negativo == true){
                for($i=0; $i<count($detalles); $i++) {
                    $articulo = Articulo::findOrFail( $detalles[$i]['id_articulo'] );
                    $articulo->stock = $articulo->stock - $detalles[$i]['cantidad'];

                    if($articulo->stock == 0)
                    {
                        $articulo->estado = 0;
                    }
                    $articulo->save();

                    $detalle = new DetalleVenta();
                    $detalle->id_venta = $venta->id;
                    $detalle->id_articulo = $detalles[$i]['id_articulo'];
                    $detalle->cantidad = $detalles[$i]['cantidad'];
                    $detalle->precio = $articulo->precio_venta;
                    $detalle->save();
                }
            } else  {
                return response()->json(['Ocurrio un problema al querer realizar la venta' => ''],409);
            }

            /*$turno_activo->caja_fin = $turno_activo->caja_fin + $venta->total;
            $turno_activo->save();*/
            /*$fecha_actual = date( 'Y-m-d' );
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
            return response()->json(['data'=>$venta],200); 
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json(['Sistema'=> $e->getMessage()],409);
        }
    }

    public function darBajaEstado(Request $request) {
        try{
            DB::beginTransaction();
                $venta = Venta::findOrFail( $request->id );

                $detalles = DetalleVenta::where('id_venta', $venta->id)->get();      // lista de detalles
                foreach ($detalles as $key => $value) {
                    $articulo = Articulo::findOrFail( $value->id_articulo );
                    $articulo->stock = $articulo->stock + $value->cantidad;
                    $articulo->estado = 1;
                    $articulo->save();
                }

                $venta->estado = Venta::ESTADO_ANULADO;
                $venta->save();

            DB::commit();
            return response()->json(['data'=>$venta],200); 
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json(['Sistema'=> 'Ocurrio un error'],409);
        }        
    }

    public function show($id) {
        
    }
    public function edit($id) {
        
    }
    public function update(Request $request, $id) {
        
    }
    public function destroy($id) {
        //
    }

    public function getVentaReciboPDF( Request $request, $id ) {
        $venta = Venta::join('personas', 'ventas.id_cliente', '=', 'personas.id')
            ->join('users', 'ventas.id_usuario', '=', 'users.id_persona')
            ->join('personas AS vendedor', 'users.id_persona','=','vendedor.id')
            ->select(   'ventas.id','ventas.tipo_comprobante','ventas.numero_comprobante',
                        'ventas.serie_comprobante', 'ventas.created_at',
                        'ventas.fecha_hora','ventas.impuesto',
                        'ventas.total','ventas.descuento','ventas.estado',
                        'personas.nombre', 'personas.tipo_documento', 'personas.numero_documento',
                        'personas.direccion', 'personas.telefono', 'personas.email',
                        'users.usuario', 'vendedor.nombre AS vendedor')
            ->where('ventas.id', '=', $id)
            ->orderBy('ventas.id', 'desc')->take(1)->get();
        
        $detalles = DetalleVenta::join('articulos', 'detalle_ventas.id_articulo', '=', 'articulos.id')
            ->select(   'detalle_ventas.cantidad','detalle_ventas.precio', 
            'detalle_ventas.descuento', 'articulos.nombre', 'articulos.codigo', 'articulos.descripcion' )
            ->where('detalle_ventas.id_venta','=',$id)
            ->orderBy('detalle_ventas.id', 'desc')->get();
        
        $numero_venta = Venta::select('numero_comprobante')->where('id',$id)->get();

        //return view( 'pdf.venta_pdf', compact( ['venta','detalles'] ) );

        $pdf= \PDF::loadView( 'pdf.venta_recibo_pdf', [
            'venta'=>$venta, 
            'detalles'=>$detalles, 
            'numero_venta'=>$numero_venta
        ] );

        return $pdf->stream( 'venta-' . $numero_venta[0]->numero_comprobante . '.pdf' );
    }

    public function getVentaFacturaPDF( Request $request, $id ) {
        $venta = Venta::join('personas', 'ventas.id_cliente', '=', 'personas.id')
            ->join('users', 'ventas.id_usuario', '=', 'users.id_persona')
            ->select(   'ventas.id','ventas.tipo_comprobante','ventas.numero_comprobante',
                        'ventas.serie_comprobante', 'ventas.created_at',
                        'ventas.fecha_hora','ventas.impuesto',
                        'ventas.total','ventas.descuento','ventas.estado',
                        'personas.nombre', 'personas.tipo_documento', 'personas.numero_documento',
                        'personas.direccion', 'personas.telefono', 'personas.email',
                        'users.usuario' )
            ->where('ventas.id', '=', $id)
            ->orderBy('ventas.id', 'desc')->take(1)->get();
        
        $detalles = DetalleVenta::join('articulos', 'detalle_ventas.id_articulo', '=', 'articulos.id')
            ->select(   'detalle_ventas.cantidad','detalle_ventas.precio', 
            'detalle_ventas.descuento', 'articulos.nombre', 'articulos.codigo', 'articulos.descripcion' )
            ->where('detalle_ventas.id_venta','=',$id)
            ->orderBy('detalle_ventas.id', 'desc')->get();
        
        $numero_venta = Venta::select('numero_comprobante')->where('id',$id)->get();

        //return view( 'pdf.venta_pdf', compact( ['venta','detalles'] ) );
        $dia = date("d",strtotime($venta[0]->fecha_hora));
        $mes = date("m",strtotime($venta[0]->fecha_hora));
        $anio = date("Y",strtotime($venta[0]->fecha_hora));
        $filas = count($detalles);
        $printFilas = '';
        for($i=1;$i<=17-$filas;$i++){
            $printFilas=$printFilas.'<tr><td>&nbsp;</td></tr>';
        }

        $pdf= \PDF::loadView( 'pdf.venta_factura_pdf', [
            'venta'=>$venta, 
            'detalles'=>$detalles, 
            'numero_venta'=>$numero_venta,
            'dia'=>$dia,
            'mes'=>$mes,
            'anio'=>$anio,
            'filas'=>$printFilas,
        ] );

        return $pdf->stream( 'venta-' . $numero_venta[0]->numero_comprobante . '.pdf' );
    }

    public function validaciones(Request $request, $id, $cf=false){
        $messages = [
            'id_cliente.required' => 'El cliente es obligatoria',
            'id_cliente.integer' => 'El cliente tiene que ser un ID',
            'id_cliente.exists' => 'El cliente seleccionada no existe en la base de datos',
            'total.required' => 'El total de la factura es obligatoria',
            'total.numeric' => 'El total de la factura no puede contener letras',
            'data.id_articulo.required' => 'El articulo es obligatoria',
            'data.id_articulo.integer' => 'El articulo tiene que ser un ID',
            'data.id_articulo.exists' => 'El articulo seleccionada no existe en la base de datos',
            'data.cantidad.required' => 'La cantidad del detalle de la factura es obligatoria',
            'data.cantidad.numeric' => 'La cantidad del detalle de la factura no puede contener letras',

            'cliente.required' => 'El nombre del cliente es obligatorio',
            'cliente.unique' => 'El nombre del cliente ya existe',
            'nit.required' => 'El NIT del cliente es obligatorio',
            'nit.unique' => 'El NIT del cliente ya existe',           
        ];

        if($request->id_cliente == 0 || $request->id_cliente == '')
        {
            if($cf == false)
            {
                $rules = [
                    'cliente' => 'required|max:110|unique:personas,nombre',
                    'nit' => 'required|unique:personas,numero_documento',
                    'direccion' => 'max:250|nullable',

                    'total' => 'required|numeric',
                    'data.id_articulo.*' => 'required|integer|exists:articulos,id',
                    'data.cantidad.*' => 'required|numeric',                   
                ];
            }
            else
            {
                $rules = [
                    'cliente' => 'required|max:110|unique:personas,nombre',
                    'direccion' => 'max:250|nullable',

                    'total' => 'required|numeric',
                    'data.id_articulo.*' => 'required|integer|exists:articulos,id',
                    'data.cantidad.*' => 'required|numeric',                   
                ]; 
            }
        }
        else
        {
            $rules = [
                'id_cliente' => 'required|integer|exists:personas,id',
                'total' => 'required|numeric',
                'data.id_articulo.*' => 'required|integer|exists:articulos,id',
                'data.cantidad.*' => 'required|numeric',
            ];
        }

        $validator = $request->validate($rules,$messages);

        return $validator;
    }       
}
