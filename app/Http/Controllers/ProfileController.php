<?php

namespace App\Http\Controllers;

// Modelos
use App\Models\DataUser;
use App\Models\Role;

// Requests personalizados
use App\Http\Requests\ProfileUpdateRequest;

// Facades
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    public function edit() : View
    {
        $roles = Role::all();
        return view('profile.edit',['roles' => $roles]);
    }

    public function update(ProfileUpdateRequest $request, DataUser $user): RedirectResponse
    {
        try {
            if ($request->hasFile('profile_img')) {
                $imagen = $request->file('profile_img');
                $directorio = public_path('img/img_perfil/' . Auth::id());

                if (!File::exists($directorio)) {
                    File::makeDirectory($directorio, 0755, true);
                }

                $nombreImagen = uniqid() . '_' . $imagen->getClientOriginalName();
                $imagen->move($directorio, $nombreImagen);
                $user->url_img = $nombreImagen;
            }

            $user->update(
                array_merge(
                    $request->only([
                        'document',
                        'birthdate',
                        'phone',
                        'address',
                        'eps',
                        'afp',
                        'arl',
                        'ethnicity',
                        'caja',
                        'rh'
                    ]),
                    [
                        'sex' => $request->gender,
                        'contract' => $request->contract_number . "-" . $request->contract_vig,
                    ]
                )
            );

            $userUpdate = $request->only(['name', 'last_name']);

            if ($request->role_id) {
                $userUpdate['role_id'] = $request->role_id;
            }
            
            $request->user()->update($userUpdate);

            return Redirect::back()->with('success', 'Perfil actualizado');
        } catch (\Throwable $th) {
            Log::error('Error al actualizar el perfil: ' . $th->getMessage());
            return Redirect::route('profile.edit')->with('error', 'Hubo un error al actualizar el perfil');
        }
    }

    public function showNewFeature()
    {
        return with('success', '¡Nuevo botón de funcionalidad añadido!');
    }

    public function updateTheme($tema, $id)
    {
        try {
            $id_user = $id;
            $user = DataUser::where('id_user', $id_user)->first();

            if ($user) {
                $user->tema = $tema;
                $user->save();
                return response()->json(['resultados' => 'Tema guardado']);
            } else {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al guardar tema'], 500);
        }
    }

    public function profileGesi (){
        $profiles = DataUser::select('id', 'url_img')->whereHas('user', function ($query){
            $query->where('environment_id', 0);
        })->get();

        if ($profiles->isEmpty()) {
            return response()->json([
                'error' => 'No se encontraron perfiles'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => $profiles
        ], Response::HTTP_OK);
    }
}