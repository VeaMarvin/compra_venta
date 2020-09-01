<?php

namespace App\Http\Middleware;
use Closure;

class Vendedor {
    
    public function handle($request, Closure $next) {
        return $next($request);
    }
    
}
