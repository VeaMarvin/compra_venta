<?php

namespace App\Http\Controllers;

use App\Talonario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TalonarioController extends Controller
{
    public function index(Request $request)
    {
        $criterio = $request->criterio;
        $buscar = $request->buscar;

        if($buscar == '') {
            $talonarios = Talonario::orderBy('id', 'desc')
                ->paginate(10);                   // eloquent
        }
        else{
            $talonarios = Talonario::where($criterio, 'like', '%' . $buscar . '%')
                ->orderBy('id', 'desc')
                ->paginate(10);
        }

        return [
            'paginacion' => [
                'total' => $talonarios->total(),
                'pagina_actual' => $talonarios->currentPage(),
                'por_pagina' => $talonarios->perPage(),
                'ultima_pagina' => $talonarios->lastPage(),
                'desde' => $talonarios->firstItem(),
                'hasta' => $talonarios->lastItem()
            ],
            'talonarios' => $talonarios
        ];
    }

    public function listaTalonarios(Request $request) {
        $talonarios = Talonario::where('activo', '=', '1')
            ->select('id', 'serie', 'inicio', 'fin')->orderBy('nombre', 'asc')->get();
        return [
            'talonarios' => $talonarios
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

            $activo = Talonario::where('activo',1)->first();
            
            if(!is_null($activo)){ 
                $siguiente = (int)$activo->fin+1;
                if((int)$activo->fin+1 != $request->inicio)
                    return response()->json(['El número correcto para seguir el correlativo es '.$siguiente => ''],409);
            }

            $verificar = Talonario::where('serie',$request->serie)->where('fin','>',$request->inicio)->first();
            $activos = Talonario::where('serie',$request->serie)->get();

            if(!is_null($verificar)){                
                return response()->json(['Talonario incorrecto, el correlativo del talonario no tiene secuencia' => ''],409);
            }


            foreach ($activos as $key => $value) {
                $talonario = Talonario::findOrFail( $value->id );
                $talonario->activo = '0';
                $talonario->save();
            }

            $talonario = new Talonario();
            $talonario->serie = $request->serie;
            $talonario->inicio = $request->inicio;   
            $talonario->fin = $request->fin;          
            $talonario->save();
    
            DB::commit();
            return response()->json(['data'=>$talonario],200); 
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json(['Sistema'=> $e->getMessage()],409);
        }
    }

    public function show(Talonario $talonario)
    {
        //
    }

    public function edit(Talonario $talonario)
    {
        //
    }

    public function update(Request $request, Talonario $talonario)
    {
        $this->validaciones($request,$request->id);
        try {

            $verificar = Talonario::where('serie',$request->serie)->where('fin','>',$request->inicio)->first();

            if(!is_null($verificar))
                return response()->json(['Talonario incorrecto, el correlativo del talonario no tiene secuencia' => ''],409);

            $talonario = Talonario::findOrFail( $request->id );
            $talonario->serie = $request->serie;
            $talonario->inicio = $request->inicio;   
            $talonario->fin = $request->fin;        
            $talonario->save();
    
            return response()->json(['data'=>$talonario],200); 
        } catch (\Exception $e) {
            return response()->json(['Sistema'=> 'Ocurrio un error'],409);
        }
    }

    public function destroy(Talonario $talonario)
    {
        //
    }

    public function darBajaEstado(Request $request) {
        $talonario = Talonario::findOrFail( $request->id );
        $talonario->activo = '0';
        $talonario->save();
    }

    public function darAltaEstado(Request $request) {
        $talonario = Talonario::findOrFail( $request->id );
        $talonario->activo = '1';
        $talonario->save();
    }

    public function validaciones(Request $request, $id=0){

        $messages = [
            'serie.required' => 'La serie del talonario es obligatorio',
            'inicio.required' => 'El número de inicio del talonario es obligatorio',
            'inicio.integer' => 'El número de inicio no acepta letras',
            'fin.required' => 'El número de fin del talonario es obligatorio',
            'fin.integer' => 'El número de fin no acepta letras',
        ];

        $rules = [
            'serie' => 'required|max:2',
            'inicio' => 'required|integer',
            'fin' => 'required|integer',
        ];

        $validator = $request->validate($rules, $messages);

        return $validator;
    }   
}
