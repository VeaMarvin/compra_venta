<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model {

    const ESTADO_REGISTRADO = 'REGISTRADO';
    const ESTADO_ANULADO = 'ANULADO'; 
    const COMPROBANTE = 'FACTURA';
    const IMPUESTO = '12.5';

    protected $table = 'ingresos';

    protected $fillable = [
        'id_proveedor',
        'id_usuario',
        'tipo_comprobante',
        'serie_comprobante',
        'numero_comprobante',
        'fecha_hora',
        'impuesto',
        'total',
        'estado',
    ];

    public function proveedor() {
        return $this->belongsTo('App\Proveedor');
    }

    public function usuario() {
        return $this->belongsTo('App\User');
    }

}
