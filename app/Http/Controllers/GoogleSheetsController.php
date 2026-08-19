<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleSheetsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class GoogleSheetsController extends Controller
{
    protected $sheetsService;

    public function __construct(GoogleSheetsService $sheetsService)
    {
        $this->sheetsService = $sheetsService;
    }

    public function show(Request $request)
    {
        $usuario = Auth::user();
        if (!$usuario) {
            return [];
        }
        $nombreUsuario = $usuario->name . " " . $usuario->last_name;
        $erroresPorSeccion = $this->sheetsService->obtenerErroresPorUsuario($nombreUsuario, $request->entorno);
        return view('dig.errors.my-errors', compact('erroresPorSeccion'))->with('erroresPorSeccion', $erroresPorSeccion);
    }
}
