<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    const CUADRO_SI = 'SI';
    const CUADRO_NO = 'NO';
    
    protected $table = 'turnos';
    protected $fillable = [
        'id_entrega',
        'id_recibe',
        'caja_inicio',
        'caja_fin',
        'fecha_recibe',
        'fecha_entrega',
        'tiempo_transcurrido',
        'activo',
        'cuadro'
    ];    
}
