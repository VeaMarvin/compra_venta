<?php

namespace App\Http\Middleware;
use Closure;

class Administrador {

    public function handle($request, Closure $next) {
        return $next($request);
    }
    
}
