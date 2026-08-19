<?php

namespace App\Http\Controllers;

use App\Mail\PruebaTecResultado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PruebaTecController extends Controller
{
    public function enviarResultados(Request $request)
    {
        $data = $request->validate([
            'candidato' => 'required|string|max:255',
            'score' => 'required|integer',
            'total_secs' => 'required|integer',
            'modulos' => 'required|array',
        ]);

        $destinatarios = [
            'monhabell@gmail.com',
            'gesi.educativo@gmail.com',
            'gesi.cordinador@gmail.com',
            'gesiinstitucional@gmail.com',
            'baquerohernandezangiebaquero@gmail.com',
            'seleccionops3@subrednorte.gov.co',
        ];

        Mail::to($destinatarios)->send(new PruebaTecResultado(
            $data['candidato'],
            $data['score'],
            $data['total_secs'],
            $data['modulos']
        ));

        return response()->json(['ok' => true]);
    }
}
