<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model {
    
    protected $table = 'articulos';
    protected $fillable = [
        'id_categoria',
        'codigo',
        'nombre',
        'precio_venta',
        'stock',
        'descripcion',
        'estado'
    ];

    public function setCodigoAttribute($value)
    {
        $this->attributes['codigo'] = mb_strtoupper($value);
    }

    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = mb_strtoupper($value);
    }

    public function setDescripcionAttribute($value)
    {
        $this->attributes['descripcion'] = mb_strtoupper($value);
    }

    public function categoria() {
        return $this->belongsTo('App\Categoria');
    }
}
