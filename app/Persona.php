<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model {

    const TIPO_DOCUMENTO = 'NIT';

    protected $table = 'personas';
    protected $fillable = [
        'nombre',
        'tipo_documento',
        'numero_documento',
        'direccion',
        'telefono',
        'email'
    ];

    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = mb_strtoupper($value);
    }

    public function setTipoDocumentoAttribute($value)
    {
        $this->attributes['tipo_documento'] = mb_strtoupper($value);
    }

    public function setNumeroDocumentoAttribute($value)
    {
        $this->attributes['numero_documento'] = mb_strtoupper($value);
    }

    public function setDireccionAttribute($value)
    {
        $this->attributes['direccion'] = mb_strtoupper($value);
    }

    public function setTelefonoAttribute($value)
    {
        $this->attributes['email'] = mb_strtolower($value);
    }

    public function proveedor() {
        return $this->hasOne('App\Proveedor');
    }
    public function user(){
        return $this->hasOne('App\User');
    }
}
