<?php

namespace App\Http\Controllers;

use App\Deposito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DepositoController extends Controller
{
    public function index(Request $request)
    {
        $criterio = $request->criterio;
        $buscar = $request->buscar;

        if($criterio == 'nombre')
            $tabla = 'personas';
        else
            $tabla = 'depositos';

        if($buscar == '') {
            if(Auth::user()->id_rol == 1) {
                $depositos = Deposito::join('personas', 'depositos.id_empleado', '=', 'personas.id')
                ->select(   'depositos.id', 'depositos.boleta', 'depositos.foto', DB::raw('DATE_FORMAT(depositos.fecha, "%d-%m-%Y") as fecha'),
                            'depositos.monto', 'personas.nombre' )
                ->orderBy('depositos.fecha', 'desc')->paginate(10);
            }
            else{
                $depositos = Deposito::join('personas', 'depositos.id_empleado', '=', 'personas.id')
                ->select(   'depositos.id', 'depositos.boleta', 'depositos.foto', DB::raw('DATE_FORMAT(depositos.fecha, "%d-%m-%Y") as fecha'),
                            'depositos.monto', 'personas.nombre' )
                ->where('depositos.id_empleado', Auth::user()->id_persona)
                ->orderBy('depositos.fecha', 'desc')->paginate(10);
            }
        }
        else{
            if(Auth::user()->id_rol == 1) {
                $depositos = Deposito::join('personas', 'depositos.id_empleado', '=', 'personas.id')
                ->select(   'depositos.id', 'depositos.boleta', 'depositos.foto', DB::raw('DATE_FORMAT(depositos.fecha, "%d-%m-%Y") as fecha'),
                            'depositos.monto', 'personas.nombre' )
                ->whereBetween(DB::raw('DATE_FORMAT(depositos.fecha, "%Y-%m-%d")'), [$buscar, date('Y-m-d')])
                ->orderBy('depositos.fecha', 'desc')->paginate(10);
            }
            else{
                $depositos = Deposito::join('personas', 'depositos.id_empleado', '=', 'personas.id')
                ->select(   'depositos.id', 'depositos.boleta', 'depositos.foto', DB::raw('DATE_FORMAT(depositos.fecha, "%d-%m-%Y") as fecha'),
                            'depositos.monto', 'personas.nombre' )
                ->where('depositos.id_empleado', Auth::user()->id_persona)
                ->whereBetween(DB::raw('DATE_FORMAT(depositos.fecha, "%Y-%m-%d")'), [$buscar, date('Y-m-d')])
                ->orderBy('depositos.fecha', 'desc')->paginate(10);
            }
        }

        return [
            'paginacion' => [
                'total' => $depositos->total(),
                'pagina_actual' => $depositos->currentPage(),
                'por_pagina' => $depositos->perPage(),
                'ultima_pagina' => $depositos->lastPage(),
                'desde' => $depositos->firstItem(),
                'hasta' => $depositos->lastItem()
            ],
            'depositos' => $depositos
        ];
    }

    public function getDepositosPDF(Request $request) {
        
        $criterio = $request->criterio;
        $buscar = $request->buscar;

        if(Auth::user()->id_rol == 1) {
            $depositos = Deposito::join('personas', 'depositos.id_empleado', '=', 'personas.id')
            ->select(   'depositos.id', 'depositos.boleta', 'depositos.foto', DB::raw('DATE_FORMAT(depositos.fecha, "%d-%m-%Y") as fecha'),
                        'depositos.monto', 'personas.nombre' )
            ->whereBetween(DB::raw('DATE_FORMAT(depositos.fecha, "%Y-%m-%d")'), [$buscar, date('Y-m-d')])
            ->orderBy('depositos.fecha', 'desc')->paginate(10);
        }
        else{
            $depositos = Deposito::join('personas', 'depositos.id_empleado', '=', 'personas.id')
            ->select(   'depositos.id', 'depositos.boleta', 'depositos.foto', DB::raw('DATE_FORMAT(depositos.fecha, "%d-%m-%Y") as fecha'),
                        'depositos.monto', 'personas.nombre' )
            ->where('depositos.id_empleado', Auth::user()->id_persona)
            ->whereBetween(DB::raw('DATE_FORMAT(depositos.fecha, "%Y-%m-%d")'), [$buscar, date('Y-m-d')])
            ->orderBy('depositos.fecha', 'desc')->paginate(10);
        }

        $cantidad = count($depositos);
        $total = DB::table('depositos')->whereBetween(DB::raw('DATE_FORMAT(depositos.fecha, "%Y-%m-%d")'), [$buscar, date('Y-m-d')])->sum('depositos.monto');
        $pdf = \PDF::loadView( 'pdf.depositos_pdf', ['depositos'=>$depositos, 'cantidad'=>$cantidad, 'total'=>$total] );
        return $pdf->download( 'depositos.pdf' );        

    }

    public function listaDepositos(Request $request) {
        $depositos = Deposito::where('id_empleado',Auth::user()->id_persona)
            ->select('boleta', 'foto', 'fecha', 'monto', 'id_empleado')->get();
        return [
            'depositos' => $depositos
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

            $img_data = file_get_contents($request->file('foto'));  
            $base64 = base64_encode($img_data);

            $file = $request->file('foto');
            $name = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
    
                $deposito = new Deposito();
                $deposito->id_empleado = Auth::user()->id_persona;
                $deposito->boleta = $request->boleta;   
                $deposito->foto = 'data:image/'.$ext.';base64,'.$base64; 
                $deposito->fecha = $request->fecha;   
                $deposito->monto = $request->monto;        
                $deposito->save();

            DB::commit();
            return response()->json(['data'=>$deposito],200); 
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json(['Sistema'=> $e->getMessage()],409);
        }
    }

    public function show(Deposito $deposito)
    {
        //
    }

    public function edit(Deposito $deposito)
    {
        //
    }

    public function update(Request $request, Deposito $deposito)
    {
        //$this->validaciones($request,$deposito->id);
        try {

            $eliminado = $deposito;             
            $deposito->delete();
    
            return response()->json(['data'=>$eliminado],200); 
        } catch (\Exception $e) {
            return response()->json(['Sistema'=> $e->getMessage()],409);
        }
    }

    public function destroy(Deposito $deposito)
    {
        //
    }

    public function validaciones(Request $request, $id=0){
        $max_size = (int)ini_get('upload_max_filesize') * 1000;
        $image_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $messages = [
            'boleta.required' => 'El número de la boleta del deposito es obligatorio',
            'boleta.unique' => 'El número de la boleta del deposito ya existe',
            'foto.required' => 'La imagen es obligatorio',
            'fecha.required' => 'La fecha del deposito es obligatoria',
            'fecha.date' => 'La fecha no tiene el formato correcto',
            'monto.numeric' => 'El monto del deposito tiene que ser un número positivo',
            'monto.required' => 'El monto del deposito es obligatoria',
        ];

        if($id > 0){
            $rules = [
                'boleta' => 'required|unique:depositos,boleta,'.$id,
                'foto' => 'required|image|max:' . $max_size,
                'fecha' => 'required|date',
                'monto' => 'required|numeric',
            ];
        }
        else{
            $rules = [
                'boleta' => 'required|unique:depositos,boleta',
                'foto' => 'required|image|max:' . $max_size,
                'fecha' => 'required|date',
                'monto' => 'required|numeric',
            ];
        }

        $validator = $request->validate($rules,$messages);

        return $validator;
    }   
}
