<?php

namespace App\Http\Middleware;

// Modelos
use App\Models\DataUser;
use App\Models\Entorno;

// Closure
use Closure;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class UserDataMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();

        if ($route) {
            $currentRouteName = $route->getName();
            
            if ($currentRouteName !== 'login.form') {
                $user = Auth::user();

                if (!$user) {
                    return redirect()->route('login.form')
                        ->with('session_expired', 'Su sesión ha expirado, por favor inicie sesión nuevamente.');
                }
                                
                $environments = Entorno::whereNot('id', 1)->get();

                $data_user = DataUser::where('id_user', $user->id)->first();
                $pendingRequestsCount = \App\Models\Reception::where('status', 1)
                    ->count();
                
                View::share([
                    'user' => $user,
                    'environments' => $environments,
                    'mostrarModal' => !$data_user,
                    'data_user' => $data_user,
                    'pendingRequestsCount' => $pendingRequestsCount
                ]);
            }
        }
        return $next($request);
    }
}