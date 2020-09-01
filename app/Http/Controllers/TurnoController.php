<?php

namespace App\Http\Controllers;

use App\Turno;
use App\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use DateTime;

class TurnoController extends Controller
{
    public function index(Request $request)
    {
        $criterio = $request->criterio;
        $buscar = $request->buscar;

        if(Auth::user()->id_rol == 1) {
            $turnos = Turno::join('personas as persona_e', 'turnos.id_entrega', '=', 'persona_e.id')
            ->join('personas as persona_r', 'turnos.id_recibe', '=', 'persona_r.id')
            ->select(   'turnos.id', 'turnos.id_entrega', 'turnos.id_recibe', 'turnos.caja_inicio',
                        'turnos.caja_fin', 'turnos.fecha_recibe', 'turnos.fecha_entrega', 'turnos.tiempo_transcurrido', 'turnos.activo',
                        'persona_e.nombre as nombre_entrega', 'persona_r.nombre as nombre_recibe' )
            ->orderBy('turnos.fecha_entrega', 'desc')->paginate(10);
        }
        else{
            $turnos = Turno::join('personas as persona_e', 'turnos.id_entrega', '=', 'persona_e.id')
            ->join('personas as persona_r', 'turnos.id_recibe', '=', 'persona_r.id')
            ->select(   'turnos.id', 'turnos.id_entrega', 'turnos.id_recibe', 'turnos.caja_inicio',
                        'turnos.caja_fin', 'turnos.fecha_recibe', 'turnos.fecha_entrega', 'turnos.tiempo_transcurrido', 'turnos.activo',
                        'persona_e.nombre as nombre_entrega', 'persona_r.nombre as nombre_recibe' )
            ->where('turnos.id_recibe',Auth::user()->id_persona)
            ->orderBy('turnos.fecha_recibe', 'desc')->paginate(10);
        }

        return [
            'paginacion' => [
                'total' => $turnos->total(),
                'pagina_actual' => $turnos->currentPage(),
                'por_pagina' => $turnos->perPage(),
                'ultima_pagina' => $turnos->lastPage(),
                'desde' => $turnos->firstItem(),
                'hasta' => $turnos->lastItem()
            ],
            'turnos' => $turnos
        ];
    }

    public function listaTurnos(Request $request) {
        $turnos = Turno::where('id_recibe',Auth::user()->id_persona)->where('activo', '=', '1')
            ->select('id', 'caja_inicio', 'caja_fin', 'fecha_recibe', 'fecha_entrega', 'tiempo_transcurrido')->orderBy('activo', 'asc')->get();
        return [
            'turnos' => $turnos
        ];
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validaciones($request,0);
        try {
            DB::beginTransaction();

            $ya_esta_en_turno = Turno::where('id_recibe',Auth::user()->id_persona)->where('activo',true)->first();

            if(!is_null($ya_esta_en_turno))
                return response()->json(['Actualmente usted tiene un turno activo'=> ''],409);

            $turno = new Turno();
            $turno->id_entrega = $request->id_recibe;
            $turno->id_recibe = Auth::user()->id_persona;   
            $turno->caja_inicio = $request->caja_inicio; 
            $turno->fecha_recibe = date('Y-m-d H:i:s');          
            $turno->save();
    
            DB::commit();
            return response()->json(['data'=>$turno],200); 
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json(['Sistema'=> $e->getMessage()],409);
        }
    }

    public function show(Turno $talonario)
    {
        //
    }

    public function edit(Turno $talonario)
    {
        //
    }

    public function update(Request $request, Turno $talonario)
    {

    }

    public function destroy(Turno $talonario)
    {
        //
    }

    public function darAltaEstado(Request $request) {
        $this->validaciones($request,$request->id);
        try {
            DB::beginTransaction();
            $total = 0;

            $ventas_realizadas = Venta::where('id_usuario',Auth::user()->id_persona)->where('estado','!=',Venta::ESTADO_ANULADO)->get();

            foreach ($ventas_realizadas as $key => $value) {
                $total = $total + $value->total;
            }

            $turno = Turno::findOrFail( $request->id );
            $total = $total + $turno->caja_inicio;

            if($total > $request->caja_fin) {
                $resta = $total-$request->caja_fin;
                return response()->json(['La caja no cuadra hay un deficit de: Q '.$resta => ''],409);
            }

            if($total < $request->caja_fin) {
                $resta = $request->caja_fin-$total;
                return response()->json(['La caja no cuadra hay un saldo de: Q '.$resta => ''],409);
            }

            if($total == $request->caja_fin)
            {
                $turno->fecha_entrega = date('Y-m-d H:i:s');
                $turno->caja_fin = $request->caja_fin; 
    
                if(!is_null($turno)){
                    $fecha1 = new DateTime($turno->fecha_recibe);
                    $fecha2 = new DateTime($turno->fecha_entrega);
                    $fecha = $fecha1->diff($fecha2);
                    $intervalo = "$fecha->m días, $fecha->h horas con $fecha->i minutos y $fecha->s segundos";
                    $turno->tiempo_transcurrido = $intervalo;
                    $turno->save();
                }
    
                $turno->activo = '0';
                $turno->save();
        
                DB::commit();
                return response()->json(['data'=>$turno],200); 
            }
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json(['Sistema'=> $e->getMessage()],409);
        }
    }

    public function validaciones(Request $request, $id=0){

        $messages = [
            'id_recibe.required' => 'El empleado que recibe es obligatorio',
            'id_recibe.integer' => 'El empleado no acepta letras',
            'caja_inicio.required' => 'El monto de la caja inicial es obligatorio',
            'caja_inicio.numeric' => 'El monto de la caja inicial no acepta letras',
            'id_recibe.exists' => 'El empleado seleccionado no existe en la base de datos',
            'caja_fin.required' => 'El monto de la caja final es obligatorio',
            'caja_fin.numeric' => 'El monto de la caja final no acepta letras',
        ];

        if($id==0)
        {
            $rules = [
                'id_recibe' => 'required|integer|exists:personas,id',
                'caja_inicio' => 'required|numeric',
            ];
        }
        else
        {
            $rules = [
                'caja_fin' => 'required|numeric',
            ];
        }

        $validator = $request->validate($rules, $messages);

        return $validator;
    }   
}
