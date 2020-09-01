<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model {
    
    protected $table = 'categorias';
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado'
    ];

    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = mb_strtoupper($value);
    }

    public function setDescripcionAttribute($value)
    {
        $this->attributes['descripcion'] = mb_strtoupper($value);
    }

    public function articulos() {
        return $this->hasMany('App\Articulo');
    }
}
