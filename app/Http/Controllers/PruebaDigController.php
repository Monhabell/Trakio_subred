<?php

namespace App\Http\Controllers;

use App\Mail\PruebaDigResultado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PruebaDigController extends Controller
{
    public function enviarResultados(Request $request)
    {
        $data = $request->validate([
            'digitador' => 'required|string|max:255',
            'score' => 'required|integer',
            'total_secs' => 'required|integer',
            'fichas' => 'required|array',
            'mecanografia' => 'nullable|array',
        ]);

        $destinatarios = [
            'monhabell@gmail.com',
            'gesi.educativo@gmail.com',
            'gesi.cordinador@gmail.com',
            'gesiinstitucional@gmail.com',
            'baquerohernandezangiebaquero@gmail.com',
            'seleccionops3@subrednorte.gov.co',
        ];

        Mail::to($destinatarios)->send(new PruebaDigResultado(
            $data['digitador'],
            $data['score'],
            $data['total_secs'],
            $data['fichas'],
            $data['mecanografia'] ?? null
        ));

        return response()->json(['ok' => true]);
    }
}
