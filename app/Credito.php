<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Credito extends Model
{
    protected $table = 'creditos';
    protected $fillable = [
        'id_venta',
        'abono',
        'fecha_hora',
    ];
}
