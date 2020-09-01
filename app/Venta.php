<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model {

    const ESTADO_REGISTRADO = 'FACTURADO';
    const ESTADO_ANULADO = 'ANULADO'; 
    const COMPROBANTE = 'TICKET';
    const IMPUESTO = '12.5';

    protected $table = 'ventas';
    protected $fillable = [
        'id_cliente',
        'id_usuario',
        'tipo_comprobante',
        'serie_comprobante',
        'numero_comprobante',
        'fecha_hora',
        'impuesto',
        'total',
        'descuento',
        'estado',
        'id_talonario'
    ];
}
