<?php

namespace App\Http\Controllers;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use App\Http\Requests\StoreLoginRequest;


class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth/login');
    }

    public function login(StoreLoginRequest $request)
    {
        try {
            $credentials = $this->validateCredentials($request);

            if (Auth::attempt($credentials, $this->shouldRemember($request))) {
                return $this->handleSuccessfulLogin(Auth::user());
            }

            return $this->handleFailedLogin();
        } catch (MethodNotAllowedHttpException $e) {
            return response()->view('session.expired');
        }
    }


    private function validateCredentials(StoreLoginRequest $request)
    {
        // Solo devolvemos email y password como credenciales
        return $request->only('email', 'password');
    }


    private function shouldRemember(Request $request)
    {
        return $request->has("remember");
    }

    public function handleSuccessfulLogin($user)
    {
        if ($user->is_active == 1) {
            return Redirect::route('app.home');
        }

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login.form')->withErrors(['message' => 'Usuario inactivo, por favor contacte al administrador']);
    }

    private function handleFailedLogin()
    {
        return Redirect::back()->withErrors(['message' => 'Credenciales inválidas. Por favor, intente de nuevo.']);
    }

    public function logout(Request $request)
    {
        // Desautenticar al usuario
        Auth::logout();

        // Invalidar la sesión
        $request->session()->invalidate();

        // Regenerar el token de la sesión
        $request->session()->regenerateToken();

        // Eliminar los cookies de sesión
        Cookie::forget('laravel_session');
        Cookie::forget('XSRF-TOKEN');

        // Redirigir al usuario a la página de inicio
        return redirect('/');
    }
}
