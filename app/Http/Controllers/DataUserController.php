<?php

namespace App\Http\Controllers;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

// Modelos
use App\Models\DataUser;

// Requests personalizados
use App\Http\Requests\StoreDataUserEnvRequest;
use App\Http\Requests\StoreDataUserRequest;

class DataUserController extends Controller
{
    protected function storeDataUserGesi(StoreDataUserRequest $request)
    {
        $directorio = public_path('img/img_perfil/' . Auth::id());

        if (!File::exists($directorio)) {
            File::makeDirectory($directorio, 0755, true);
        }

        $nombreImagen = null; // Variable para almacenar el nombre de la imagen

        if ($request->hasFile('foto')) {
            $imagen = $request->file('foto');
            $nombreImagen = $imagen->getClientOriginalName();

            // Guardar la imagen en el directorio público
            $imagen->move($directorio, $nombreImagen);
        }

        try {
            DataUser::create([
                'id_user' => Auth::id(),
                'document' => $request->document,
                'phone' => $request->phone,
                'address' => $request->address,
                'rh' => $request->rh,
                'contract' => $request->contract_number . "-" . $request->contract_vig,
                'birthdate' => $request->birthdate,
                'sex' => $request->sex,
                'ethnicity' => $request->ethnicity,
                'eps' => $request->eps,
                'afp' => $request->afp,
                'arl' => $request->arl,
                'caja' => $request->caja,
                'url_img' => $nombreImagen,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al guardar los datos: ' . $e->getMessage());
        }

        return back()->with('success', 'Datos guardados correctamente');
    }

    protected function storeDataUserEnv(StoreDataUserEnvRequest $request)
    {
        try {
            DataUser::create([
                'id_user' => Auth::id(),
                'document' => $request->document,
                'birthdate' => $request->birthdate,
                'phone' => $request->phone,
                'sex' => $request->sex,
                'contract' => $request->contract_number . "-" . $request->contract_vig,
                'eps' => $request->eps,
                'afp' => $request->afp,
                'arl' => $request->arl,
                'caja' => $request->caja
            ]);
        } catch (\Exception $e) {
            back()->with('error', 'Error al guardar los datos: ' . $e->getMessage());
        }

        return back()->with('success', 'Datos guardados correctamente');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->environments_id == 0) {
            $this->storeUserGesi($request);
        } else {
            $this->storeUserEnvironment($request);
        }
    }
}