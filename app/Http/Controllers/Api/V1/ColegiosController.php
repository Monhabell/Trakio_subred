<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ColegiosController extends Controller
{
    private $rutaArchivo;

    public function __construct()
    {
        $this->rutaArchivo = public_path('js/calidad/Educativo/datajson/colegios.json');
    }

    public function guardarColegio(Request $request)
    {
        // Validar entrada
        $request->validate([
            'nombre' => 'required|string',
        ]);

        // Leer archivo JSON o inicializar estructura vacía
        $data = ["Colegios" => []];

        if (file_exists($this->rutaArchivo)) {
            try {
                $contenido = file_get_contents($this->rutaArchivo);

                // Si el archivo está vacío, inicializarlo con la estructura vacía
                if (empty(trim($contenido))) {
                    $data = ["Colegios" => []];
                } else {
                    $data = json_decode($contenido, true);
                    
                    if ($data === null) {
                        throw new \Exception("Error al decodificar el JSON: " . json_last_error_msg());
                    }
                }

                if ($data === null) {
                    throw new \Exception("Error al decodificar el JSON");
                }
            } catch (\Exception $e) {
                Log::error("Error al leer el archivo JSON: " . $e->getMessage());
                return response()->json(['mensaje' => 'Error interno al leer la base de datos'], 500);
            }
        }

        // Verificar si el colegio ya existe
        foreach ($data["Colegios"] as $colegio) {
            if (strcasecmp($colegio["nombre"], $request->nombre) === 0) {
                return response()->json([
                    'mensaje' => 'El colegio ya existe en la lista.'
                ], 409);
            }
        }

        // Agregar el nuevo colegio con estado 0
        $data["Colegios"][] = ["nombre" => $request->nombre, "estado" => 0];

        // Contar colegios con estado 0
        $colegiosParaEnviar = [];
        foreach ($data["Colegios"] as $colegio) {
            if ($colegio["estado"] === 0) {
                $colegiosParaEnviar[] = $colegio;
            }
        }

        // Guardar el JSON actualizado
        try {
            file_put_contents($this->rutaArchivo, json_encode($data, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            Log::error("Error al escribir en el archivo JSON: " . $e->getMessage());
            return response()->json(['mensaje' => 'Error al guardar la información'], 500);
        }

        // Enviar correo si hay 5 colegios con estado 0
        if (count($colegiosParaEnviar) >= 5) {
            if ($this->enviarCorreo($colegiosParaEnviar)) {
                // Cambiar estado a 1 de los colegios enviados
                foreach ($data["Colegios"] as &$colegio) {
                    if ($colegio["estado"] === 0) {
                        $colegio["estado"] = 1;
                    }
                }

                // Guardar cambios en JSON
                try {
                    file_put_contents($this->rutaArchivo, json_encode($data, JSON_PRETTY_PRINT));
                } catch (\Exception $e) {
                    Log::error("Error al actualizar estados en el archivo JSON: " . $e->getMessage());
                }
            }
        }

        return response()->json([
            'mensaje' => 'Colegio guardado correctamente',
            'data'    => $data
        ], 200);
    }

    private function enviarCorreo($colegios)
    {
        $destinatario = "monhabell@gmail.com";
        $listaColegios = implode(", ", array_map(fn($c) => $c["nombre"], $colegios));

        try {
            Mail::raw("Solucitud de colocar en el desplegable estos 5 colegios que no aparecen en la base de Sesiones Colectivas 5 colegios: " . $listaColegios, function ($message) use ($destinatario) {
                $message->to($destinatario)
                        ->subject('Solicitud de 5 colegios');
            });
            return true;
        } catch (\Exception $e) {
            Log::error("Error al enviar el correo: " . $e->getMessage());
            return false;
        }
    }
}
