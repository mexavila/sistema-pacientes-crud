<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtener token de cookie o sesión
        $token = $request->cookie('api_token') ?? $request->session()->get('api_token');
        
        if ($token) {
            // Compartir token con vistas
            view()->share('api_token', $token);
            
            // Agregar a los headers para Axios
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }
        
        return $next($request);
    }
}
