<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model {
    
    protected $fillable = [
        'id_persona',
        'contacto',
        'telefono_contacto'
    ];
    
    protected $table = 'proveedores';
    protected $primaryKey = 'id_persona';
    public $timestamps = false;
    public function persona() {
        return $this->belongsTo('App\Persona');
    }

    public function setContactoAttribute($value)
    {
        $this->attributes['contacto'] = mb_strtoupper($value);
    }
}
