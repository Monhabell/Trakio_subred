<?php

namespace App\Http\Controllers;

// Modelos
use App\Models\ActasSemanales;

// Facades
use Illuminate\Http\Request;

class TareaActasController extends Controller
{
    public function configurarTarea(Request $request)
    {
        // Validar los datos
        $request->validate([
            'subject' => 'required|string|max:255',
            'days' => 'required|array',
            'environment_id' => 'required',
            'email' => 'required',
        ]);

        ActasSemanales::updateOrCreate(
            [
                'subject' => $request->subject,
                'days' => $request->days,
                'environment_id' => $request->environment_id,
                'email' => $request->email,
            ]
        );

        return redirect()->back()->with('success', 'Configuración guardada exitosamente.');
    }
}
