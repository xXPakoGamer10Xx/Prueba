<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role  // Este es el rol que vamos a verificar
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Verifica si el usuario ha iniciado sesión.
        // 2. Verifica si el rol del usuario es el que se requiere para esta ruta.
        if (!Auth::check() || Auth::user()->rol != $role) {
            // Si alguna de las dos condiciones falla, se niega el acceso.
            abort(403, 'Acceso no autorizado.');
        }

        // Si todo es correcto, permite que la petición continúe.
        return $next($request);
    }
}
