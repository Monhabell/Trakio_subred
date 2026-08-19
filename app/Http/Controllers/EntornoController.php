<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use App\Models\Entorno;


class EntornoController extends Controller
{
    public function editarHoraDia(Request $request)
    {
        $jsonPath = storage_path('app/public/tiempo_dia.json');

        if (!file_exists($jsonPath)) {
            return response()->json(['message' => 'Archivo JSON no encontrado'], 404);
        }

        // Leer el JSON
        $jsonData = json_decode(file_get_contents($jsonPath), true);

        // Extraer los valores del request
        $entornoId = $request->id;
        $valor = (int) $request->valor;

        // Convertir la fecha (YYYY/MM/DD) a formato (DD/MM)
        $fechaOriginal = $request->fecha;

        $fechaFormateada = date('d/m', strtotime($fechaOriginal));

        // Verificar si el entorno existe, si no, crearlo
        if (!isset($jsonData['entornos'][$entornoId])) {
            $jsonData['entornos'][$entornoId] = [];
        }

        // Verificar si la fecha existe dentro del entorno, si no, agregarla
        if (!isset($jsonData['entornos'][$entornoId][$fechaFormateada])) {
            $jsonData['entornos'][$entornoId][$fechaFormateada] = ['meta' => 0];
        }

        // Actualizar o insertar el nuevo valor
        $jsonData['entornos'][$entornoId][$fechaFormateada]['meta'] = $valor;

       // Guardar el JSON actualizado sin barras invertidas
        file_put_contents($jsonPath, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));


        return response()->json(['message' => 'Entorno actualizado correctamente']);
    }

    public function index()
    {
        try {
            $environments = Entorno::all();

            return response()->json([
                'data' => $environments,
                'success' => true
            ]);
        } catch (\Exception $e) {
            Log::error("Error al obtener los entornos ".$e->getMessage());
            return response()->json(['error' => "No se pudieron obtener los entornos"], 500);
        }
    }
}
