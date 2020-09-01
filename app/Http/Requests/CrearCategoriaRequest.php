<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class CrearCategoriaRequest extends FormRequest {
    
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'nombre'=>'required|min:5|unique:categorias'
        ];
    }
}
