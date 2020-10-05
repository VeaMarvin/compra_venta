<?php

namespace App\Http\Controllers;

use App\User;
use App\Credito;
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

class CreditoController extends Controller
{
    public function index(Request $request)
    {
        //if(!$request->ajax()) return redirect( '/' );
        $criterio = $request->criterio;
        $buscar = $request->buscar;
        if ($buscar == '') {
            $ventas = Venta::join('personas', 'ventas.id_cliente', '=', 'personas.id')
                ->join('users', 'ventas.id_usuario', '=', 'users.id_persona')
                ->leftJoin('creditos', 'ventas.id', '=', 'creditos.id_venta')
                ->select(
                    'ventas.id',
                    'ventas.tipo_comprobante',
                    'ventas.numero_comprobante',
                    'ventas.serie_comprobante',
                    'ventas.impuesto',
                    DB::raw('DATE_FORMAT(ventas.fecha_hora, "%d-%m-%Y %H:%i:%s") as fecha_hora'),
                    'ventas.total',
                    'ventas.descuento',
                    'ventas.estado',
                    'personas.id as cliente_id',
                    'personas.nombre',
                    'users.usuario',
                    'ventas.created_at',
                    DB::raw('(ventas.total - SUM(creditos.abono)) AS saldo')
                )
                ->where('ventas.credito', '=', '1')
                ->groupBy('ventas.id')
                ->orderBy('ventas.id', 'desc')->paginate(10);
        } else {
            if ($criterio == 'fecha_hora') {
                $ventas = Venta::join('personas', 'ventas.id_cliente', '=', 'personas.id')
                    ->join('users', 'ventas.id_usuario', '=', 'users.id_persona')
                    ->leftJoin('creditos', 'ventas.id', '=', 'creditos.id_venta')
                    ->select(
                        'ventas.id',
                        'ventas.tipo_comprobante',
                        'ventas.numero_comprobante',
                        'ventas.serie_comprobante',
                        'ventas.impuesto',
                        DB::raw('DATE_FORMAT(ventas.fecha_hora, "%d-%m-%Y %H:%i:%s") as fecha_hora'),
                        'ventas.total',
                        'ventas.descuento',
                        'ventas.estado',
                        'personas.id as cliente_id',
                        'personas.nombre',
                        'users.usuario',
                        'ventas.created_at',
                        DB::raw('(ventas.total - SUM(creditos.abono)) AS saldo')
                    )
                    ->where('ventas.credito', '=', '1')
                    ->whereBetween(DB::raw('DATE_FORMAT(ventas.fecha_hora, "%Y-%m-%d")'), [$buscar, date('Y-m-d')])
                    ->groupBy('creditos.id_venta')
                    ->orderBy('ventas.fecha_hora', 'desc')->paginate(10);
            } elseif ($criterio == 'nombre') {
                $ventas = Venta::join('personas', 'ventas.id_cliente', '=', 'personas.id')
                    ->join('users', 'ventas.id_usuario', '=', 'users.id_persona')
                    ->leftJoin('creditos', 'ventas.id', '=', 'creditos.id_venta')
                    ->select(
                        'ventas.id',
                        'ventas.tipo_comprobante',
                        'ventas.numero_comprobante',
                        'ventas.serie_comprobante',
                        'ventas.impuesto',
                        DB::raw('DATE_FORMAT(ventas.fecha_hora, "%d-%m-%Y %H:%i:%s") as fecha_hora'),
                        'ventas.total',
                        'ventas.descuento',
                        'ventas.estado',
                        'personas.id as cliente_id',
                        'personas.nombre',
                        'users.usuario',
                        'ventas.created_at',
                        DB::raw('(ventas.total - SUM(creditos.abono)) AS saldo')
                    )
                    ->where('ventas.credito', '=', '1')
                    ->where('personas.nombre', 'like', '%' . $buscar . '%')
                    ->groupBy('creditos.id_venta')
                    ->orderBy('ventas.fecha_hora', 'desc')->paginate(10);
            } else {
                $ventas = Venta::join('personas', 'ventas.id_cliente', '=', 'personas.id')
                    ->join('users', 'ventas.id_usuario', '=', 'users.id_persona')
                    ->leftJoin('creditos', 'ventas.id', '=', 'creditos.id_venta')
                    ->select(
                        'ventas.id',
                        'ventas.tipo_comprobante',
                        'ventas.numero_comprobante',
                        'ventas.serie_comprobante',
                        'ventas.impuesto',
                        DB::raw('DATE_FORMAT(ventas.fecha_hora, "%d-%m-%Y %H:%i:%s") as fecha_hora'),
                        'ventas.total',
                        'ventas.descuento',
                        'ventas.estado',
                        'personas.id as cliente_id',
                        'personas.nombre',
                        'users.usuario',
                        'ventas.created_at',
                        DB::raw('(ventas.total - SUM(creditos.abono)) AS saldo')
                    )
                    ->where('ventas.credito', '=', '1')
                    ->where('ventas.' . $criterio, 'like', '%' . $buscar . '%')
                    ->groupBy('creditos.id_venta')
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

    public function getCabecera(Request $request)
    {
        $id = $request->id;
        $cabecera = Venta::join('personas', 'ventas.id_cliente', '=', 'personas.id')
            ->join('users', 'ventas.id_usuario', '=', 'users.id_persona')
            ->leftJoin('creditos', 'ventas.id', '=', 'creditos.id_venta')
            ->select(
                'ventas.id',
                'ventas.tipo_comprobante',
                'ventas.numero_comprobante',
                'ventas.serie_comprobante',
                'ventas.fecha_hora',
                'ventas.impuesto',
                'ventas.total',
                'ventas.descuento',
                'ventas.estado',
                'personas.nombre',
                'users.usuario',
                'ventas.created_at',
                DB::raw('(ventas.total - SUM(creditos.abono)) AS saldo')
            )
            ->where('ventas.id', '=', $id)
            ->groupBy('creditos.id_venta')
            ->orderBy('ventas.id', 'desc')->take(1)->get();
        return ['cabecera' => $cabecera];
    }

    public function getDetalles(Request $request)
    {
        $id = $request->id;
        $detalles = DetalleVenta::join('articulos', 'detalle_ventas.id_articulo', '=', 'articulos.id')
            ->select(
                'detalle_ventas.id',
                'detalle_ventas.cantidad',
                'detalle_ventas.precio',
                'detalle_ventas.descuento',
                'articulos.id',
                'articulos.nombre',
                'articulos.codigo'
            )
            ->where('detalle_ventas.id_venta', '=', $id)
            ->orderBy('detalle_ventas.id', 'desc')->get();
        return ['detalles' => $detalles];
    }

    public function getAbonos(Request $request)
    {
        $id_venta = $request->id;
        $abonos = Credito::where('id_venta', $id_venta)->get();
        return ['abonos' => $abonos];
    }

    public function getVentaListPDF(Request $request)
    {
        $criterio = $request->criterio;
        $buscar = $request->buscar;

        $ventas = Venta::join('personas', 'ventas.id_cliente', '=', 'personas.id')
            ->join('users', 'ventas.id_usuario', '=', 'users.id_persona')
            ->select(
                'ventas.id',
                'ventas.tipo_comprobante',
                'ventas.numero_comprobante',
                'ventas.serie_comprobante',
                'ventas.impuesto',
                DB::raw('DATE_FORMAT(ventas.fecha_hora, "%d-%m-%Y %H:%i:%s") as fecha_hora'),
                'ventas.total',
                'ventas.descuento',
                'ventas.estado',
                'personas.id as cliente_id',
                'personas.nombre',
                'users.usuario',
                'ventas.created_at'
            )
            ->whereBetween(DB::raw('DATE_FORMAT(ventas.fecha_hora, "%Y-%m-%d")'), [$buscar, date('Y-m-d')])
            ->orderBy('ventas.fecha_hora', 'desc')->get();

        $cantidad = count($ventas);
        $total = DB::table('ventas')->whereBetween(DB::raw('DATE_FORMAT(ventas.fecha_hora, "%Y-%m-%d")'), [$buscar, date('Y-m-d')])->sum('ventas.total');
        $pdf = \PDF::loadView('pdf.ventas_pdf', ['ventas' => $ventas, 'cantidad' => $cantidad, 'total' => $total]);
        return $pdf->download('ventas.pdf');
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        try {
            $time = Carbon::now('America/Guatemala');
            $credito = new Credito();
            $credito->id_venta = $request->id_venta;
            $credito->abono = $request->abono;
            $credito->fecha_hora = $time->toDateTimeString();
            $credito->save();
        } catch (\Exception $e) {
            return response()->json(['Sistema' => $e->getMessage()], 409);
        }
    }

    public function darBajaEstado(Request $request)
    {
        try {
            DB::beginTransaction();
            $credito = Credito::findOrFail($request->id);
            $credito->estado = Credito::ESTADO_ANULADO;
            $credito->save();

            DB::commit();
            return response()->json(['data' => $credito], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['Sistema' => 'Ocurrio un error'], 409);
        }
    }

    public function show($id)
    {
    }
    public function edit($id)
    {
    }
    public function update(Request $request, $id)
    {
        try {
            $credito = Credito::findOrFail($request->id);
            $credito->abono = 0;
            $credito->save();
        } catch (\Exception $e) {
            return response()->json(['Sistema' => 'Ocurrio un error'], 409);
        }
    }
    public function destroy($id)
    {
        //
    }

    public function getCreditoResumenPDF($id)
    {
        $venta = Venta::join('personas', 'ventas.id_cliente', '=', 'personas.id')
            ->join('users', 'ventas.id_usuario', '=', 'users.id_persona')
            ->join('personas AS vendedor', 'users.id_persona', '=', 'vendedor.id')
            ->leftJoin('creditos', 'ventas.id', '=', 'creditos.id_venta')
            ->select(
                'ventas.id',
                'ventas.tipo_comprobante',
                'ventas.numero_comprobante',
                'ventas.serie_comprobante',
                'ventas.created_at',
                'ventas.fecha_hora',
                'ventas.impuesto',
                'ventas.total',
                'ventas.descuento',
                'ventas.estado',
                'personas.nombre',
                'personas.tipo_documento',
                'personas.numero_documento',
                'personas.direccion',
                'personas.telefono',
                'personas.email',
                'users.usuario',
                'vendedor.nombre AS vendedor',
                DB::raw('(ventas.total - SUM(creditos.abono)) AS saldo')
            )
            ->where('ventas.id', '=', $id)
            ->groupBy('creditos.id_venta')
            ->orderBy('ventas.id', 'desc')->take(1)->get();

        $documento = "";
        switch ($venta[0]->tipo_comprobante) {
            case "RECIBO":
                $documento = "Recibo No. " . $venta[0]->numero_comprobante;
                break;
            case "FACTURA":
                $documento = "Factura No. " . $venta[0]->serie_comprobante . "-" . $venta[0]->numero_comprobante;
                break;
        }

        $abonos = Credito::where('id_venta', '=', $id)
            ->where('abono', '>', 0)->get();

        foreach ($abonos as $index => $abono) {
            if ($index == 0) {
                $abono->saldo = $venta[0]->total - $abono->abono;
                $abono->saldo = number_format($abono->saldo, 2, '.', ',');
            } else {
                $abono->saldo = $abonos[$index - 1]->saldo - $abono->abono;
                $abono->saldo = number_format($abono->saldo, 2, '.', ',');
            }
        }

        $detalles = DetalleVenta::join('articulos', 'detalle_ventas.id_articulo', '=', 'articulos.id')
            ->select(
                'detalle_ventas.cantidad',
                'detalle_ventas.precio',
                'detalle_ventas.descuento',
                'articulos.nombre',
                'articulos.codigo',
                'articulos.descripcion'
            )
            ->where('detalle_ventas.id_venta', '=', $id)
            ->orderBy('detalle_ventas.id', 'desc')->get();

        $numero_venta = Venta::select('numero_comprobante')->where('id', $id)->get();

        //return view( 'pdf.venta_pdf', compact( ['venta','detalles'] ) );
        $pdf = \PDF::loadView('pdf.credito_pdf', [
            'venta' => $venta,
            'abonos' => $abonos,
            'detalles' => $detalles,
            'documento' => $documento,
            'numero_venta' => $numero_venta
        ]);

        // return $pdf->stream( 'credito-venta-' . $numero_venta[0]->numero_comprobante . '.pdf' );
        return $pdf->stream('CREDITO ' . $documento . '.pdf');
    }

    public function validaciones(Request $request, $id, $cf = false)
    {
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

        if ($request->id_cliente == 0 || $request->id_cliente == '') {
            if ($cf == false) {
                $rules = [
                    'cliente' => 'required|max:110|unique:personas,nombre',
                    'nit' => 'required|unique:personas,numero_documento',
                    'direccion' => 'max:250|nullable',

                    'total' => 'required|numeric',
                    'data.id_articulo.*' => 'required|integer|exists:articulos,id',
                    'data.cantidad.*' => 'required|numeric',
                ];
            } else {
                $rules = [
                    'cliente' => 'required|max:110|unique:personas,nombre',
                    'direccion' => 'max:250|nullable',

                    'total' => 'required|numeric',
                    'data.id_articulo.*' => 'required|integer|exists:articulos,id',
                    'data.cantidad.*' => 'required|numeric',
                ];
            }
        } else {
            $rules = [
                'id_cliente' => 'required|integer|exists:personas,id',
                'total' => 'required|numeric',
                'data.id_articulo.*' => 'required|integer|exists:articulos,id',
                'data.cantidad.*' => 'required|numeric',
            ];
        }

        $validator = $request->validate($rules, $messages);

        return $validator;
    }


    //
}
