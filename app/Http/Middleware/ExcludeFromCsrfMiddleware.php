<?php

namespace App\Http\Middleware;

use Closure;

class ExcludeFromCsrfMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $routesWithoutCsrf = [
            'save_productivity_sds',
            'areas',
            'areas/*',  // Agrega tu ruta aquí
        ];

        // Comprobamos si la ruta solicitada está en la lista de rutas sin CSRF
        foreach ($routesWithoutCsrf as $route) {
            // Asegúrate de que la ruta coincida correctamente
            if ($request->is($route) || $request->is($route . '/*')) {
                // Si coincide, la ruta está excluida de la verificación de CSRF
                return $next($request);
            }
        }


        //Verificación CSRF si la ruta no está en la lista de excepciones
        if (!$request->method() === 'POST' || $request->method() === 'PUT' || $request->method() === 'DELETE') {
            if ($request->hasHeader('X-CSRF-TOKEN') || $request->header('X-CSRF-TOKEN') !== csrf_token()) {
                return response()->json(['error' => 'CSRF token mismatch.'], 419);
            }
        }

        return $next($request);
    }
}