<?php

namespace App\Http\Controllers;

use App\Articulo;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ArticuloController extends Controller
{

    public function index(Request $request)
    {
        $criterio = $request->criterio;
        $buscar = $request->buscar;
        $pagination = 10;
        if ($buscar == '') {
            $articulos = Articulo::join('categorias', 'articulos.id_categoria', '=', 'categorias.id')
                ->select(
                    'articulos.id',
                    'articulos.codigo',
                    'articulos.nombre',
                    'categorias.nombre as nombre_categoria',
                    'articulos.id_categoria',
                    'articulos.precio_venta',
                    'articulos.stock',
                    'articulos.descripcion',
                    'articulos.estado'
                )
                ->orderBy('articulos.id', 'desc')->paginate($pagination);                   // eloquent
        } else {
            $articulos = Articulo::join('categorias', 'articulos.id_categoria', '=', 'categorias.id')
                ->select(
                    'articulos.id',
                    'articulos.codigo',
                    'articulos.nombre',
                    'categorias.nombre as nombre_categoria',
                    'articulos.id_categoria',
                    'articulos.precio_venta',
                    'articulos.stock',
                    'articulos.descripcion',
                    'articulos.estado'
                )
                ->where('articulos.' . $criterio, 'like', '%' . $buscar . '%')
                ->orderBy('articulos.id', 'desc')->paginate($pagination);
        }
        return [
            'paginacion' => [
                'total' => $articulos->total(),
                'pagina_actual' => $articulos->currentPage(),
                'por_pagina' => $articulos->perPage(),
                'ultima_pagina' => $articulos->lastPage(),
                'desde' => $articulos->firstItem(),
                'hasta' => $articulos->lastItem()
            ],
            'articulos' => $articulos
        ];
    }
    public function create()
    {
    }
    public function store(Request $request)
    {
        $this->validaciones($request, 0);
        try {

            DB::beginTransaction();
            $articulo = new Articulo();
            $articulo->id_categoria = $request->id_categoria;
            $articulo->codigo = 0;
            $articulo->nombre = $request->nombre;
            $articulo->precio_venta = $request->precio_venta;
            $articulo->stock = $request->stock;
            $articulo->descripcion = $request->descripcion;
            $articulo->estado = '1';
            $articulo->save();

            $articulo->codigo = $articulo->id;
            $articulo->save();

            DB::commit();
            return response()->json(['data' => $articulo], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['Sistema' => 'Ocurrio un error'], 409);
        }
    }

    public function show($id)
    {
    }
    public function edit($id)
    {
    }
    public function update(Request $request)
    {
        $this->validaciones($request, $request->id);
        try {
            $articulo = Articulo::findOrFail($request->id);
            $articulo->id_categoria = $request->id_categoria;
            $articulo->nombre = $request->nombre;
            $articulo->precio_venta = $request->precio_venta;
            $articulo->stock = $request->stock;
            $articulo->descripcion = $request->descripcion;
            $articulo->estado = '1';
            $articulo->save();

            return response()->json(['data' => $articulo], 200);
        } catch (\Exception $e) {
            return response()->json(['Sistema' => 'Ocurrio un error'], 409);
        }
    }

    public function darBajaEstado(Request $request)
    {
        $articulo = Articulo::findOrFail($request->id);
        $articulo->estado = '0';
        $articulo->save();
    }

    public function darAltaEstado(Request $request)
    {
        $articulo = Articulo::findOrFail($request->id);
        $articulo->estado = '1';
        $articulo->save();
    }

    public function destroy($id)
    {
    }

    public function buscarArticulo(Request $request)
    {
        $filtro = $request->filtro;
        $articulos = Articulo::where('id', '=', $filtro)
            ->select('id', 'nombre')->orderBy('nombre', 'asc')
            ->take(1)->get();
        return ['articulos' => $articulos];
    }

    public function buscarArticuloVenta(Request $request)
    {
        $buscar = $request->buscar;
        $articulos = Articulo::join('categorias', 'articulos.id_categoria', '=', 'categorias.id')
            ->select(
                'articulos.id',
                'articulos.nombre',
                'articulos.id_categoria',
                'categorias.nombre as categoria',
                'articulos.codigo',
                'articulos.precio_venta',
                'articulos.stock',
                'articulos.estado'
            )
            ->where('articulos.codigo', '=', $buscar)
            ->where('articulos.stock', '>', 0)
            ->orderBy('articulos.id', 'desc')
            ->paginate(10);
        return [
            'articulos' => $articulos
        ];
    }

    public function buscarArticuloConStock(Request $request)
    {
        $filtro = $request->filtro;
        $articulos = Articulo::where('id', '=', $filtro)
            ->select('id', 'nombre', 'stock', 'precio_venta')
            ->where('stock', '>', 0)
            ->orderBy('nombre', 'asc')
            ->take(1)->get();
        return ['articulos' => $articulos];
    }

    public function getArticulos(Request $request)
    {
        $criterio = $request->criterio;
        $buscar = $request->buscar;
        if ($buscar == '') {
            $articulos = Articulo::join('categorias', 'articulos.id_categoria', '=', 'categorias.id')
                ->select(
                    'articulos.id',
                    'articulos.codigo',
                    DB::RAW('CONCAT(categorias.nombre," ",articulos.nombre) AS nombre'),
                    'articulos.precio_venta',
                    'articulos.stock',
                    'articulos.estado',
                    'articulos.descripcion'
                )
                ->orderBy('articulos.id', 'desc')->paginate(10);
        } else {
            $articulos = Articulo::join('categorias', 'articulos.id_categoria', '=', 'categorias.id')
                ->select(
                    'articulos.id',
                    'articulos.codigo',
                    DB::RAW('CONCAT(categorias.nombre," ",articulos.nombre) AS nombre'),
                    'articulos.precio_venta',
                    'articulos.stock',
                    'articulos.estado',
                    'articulos.descripcion'
                )
                ->where('articulos.' . $criterio, 'like', '%' . $buscar . '%')
                ->orderBy('articulos.id', 'desc')->paginate(10);
        }
        return ['articulos' => $articulos];
    }

    public function getArticulosPDF()
    {
        $articulos = Articulo::join('categorias', 'articulos.id_categoria', '=', 'categorias.id')
            ->select(
                'articulos.id',
                'articulos.codigo',
                'articulos.nombre',
                'categorias.nombre as nombre_categoria',
                'articulos.id_categoria',
                'articulos.precio_venta',
                'articulos.stock',
                'articulos.descripcion',
                'articulos.estado'
            )
            ->where('articulos.estado', 1)
            ->orderBy('articulos.nombre', 'desc')->get();
        $cantidad = count($articulos);
        $pdf = \PDF::loadView('pdf.articulos_pdf', ['articulos' => $articulos, 'cantidad' => $cantidad]);
        return $pdf->download('articulos.pdf');
    }

    public function validaciones(Request $request, $id)
    {
        $messages = [
            'id_categoria.required' => 'La categoría es obligatoria',
            'id_categoria.integer' => 'La categoría tiene que ser un ID',
            'id_categoria.exists' => 'La categoría seleccionada no existe en la base de datos',
            'codigo.required' => 'El código del artículo es obligatorio',
            'codigo.unique' => 'El código del artículo ya existe',
            'precio_venta.required' => 'El precio de la venta es obligatorio',
            'precio_venta.numeric' => 'El precio de la venta tiene que se un número positivo',
            'stock.required' => 'El stock es obligatorio',
            'stock.numeric' => 'El stock tiene que se un número positivo',
            'descripcion.required' => 'La descripción es obligatoria',
        ];

        if ($id > 0) {
            $rules = [
                'id_categoria' => 'required|integer|exists:categorias,id',
                'nombre' => 'required|max:50|unique:articulos,nombre,' . $id,
                'precio_venta' => 'required|numeric',
                'stock' => 'required|numeric',
                'descripcion' => 'required|max:1500',
            ];
        } else {
            $rules = [
                'id_categoria' => 'required|integer|exists:categorias,id',
                'nombre' => 'required|max:50|unique:articulos,nombre',
                'precio_venta' => 'required|numeric',
                'stock' => 'required|numeric',
                'descripcion' => 'required|max:1500',
            ];
        }

        $validator = $request->validate($rules, $messages);

        return $validator;
    }
}
