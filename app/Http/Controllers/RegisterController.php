<?php

namespace App\Http\Controllers;

// Modelos
use App\Models\User;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreRegisterRequest;

class RegisterController extends Controller
{
    /**
     * Muestra el formulario de registro.
     *
     * @return \Illuminate\View\View
     */

     public function index(){
        $roles = \App\Models\Role::all();
        $environments = \App\Models\Entorno::all();
        return view('auth/register', [
            'roles' => $roles,
            'environments' => $environments,
        ]);
     }

    /**
     * Maneja la solicitud de registro de un nuevo usuario.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRegisterRequest $request )
    {
        
        try {
            User::create([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'environment_id' => $request->environment,
                'subnet_id' => $request->subnet,
                'is_admin' => false,
                'terms_accepted' => false
            ]);
            
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error al registrarse, intente nuevamente ' . $e->getMessage());
        }
        
        return redirect()->route('login.form')->with('success', '¡Registro satisfactorio! Debe solicitar la activación de la cuenta para iniciar sesión');
    }
}