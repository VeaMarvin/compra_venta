<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    
    use Notifiable;

    protected $primaryKey = 'id_persona';

    public $timestamps = false;

    protected $fillable = [
        'id_persona',
        'usuario',
        'password',
        'estado',
        'id_rol'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    public function setUsuarioAttribute($value)
    {
        $this->attributes['usuario'] = mb_strtolower($value);
    }

    public function rol() {
        return $this->belongsTo('App\Rol');
    }

    public function persona() {
        return $this->belongsTo('App\Persona');
    }

}
