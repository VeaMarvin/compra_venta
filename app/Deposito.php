<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deposito extends Model
{
    protected $appends = ['path'];
    protected $table = 'depositos';
    protected $fillable = [
        'boleta',
        'fecha',
        'foto',
        'monto',
        'id_empleado',
    ];  

    public function setBoletaAttribute($value)
    {
        $this->attributes['boleta'] = mb_strtolower($value);
    }

    public function getPathAttribute()
    {
        return storage_path('app\\public\\' .$this->foto);
    }
}
