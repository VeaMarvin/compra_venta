<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Talonario extends Model
{
    protected $table = 'talonarios';
    protected $fillable = [
        'serie',
        'inicio',
        'fin',
        'activo',
        'descontar'
    ];

    public function setSerieAttribute($value)
    {
        $this->attributes['serie'] = mb_strtoupper($value);
    }
}
