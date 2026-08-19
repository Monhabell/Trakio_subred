<?php

namespace App\Http\Controllers;

use App\Models\FormatSds;
use App\Models\Reception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; // Asegúrate de importar la clase File
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    protected $archivoJson = 'js/odin/areas.json'; // Ubicación del archivo JSON en la carpeta public

    // Método para cargar las áreas
    public function cargarAreas()
    {
        // Verifica si el archivo JSON existe
        if (File::exists(public_path($this->archivoJson))) {
            // Si existe, carga su contenido
            $contenido = File::get(public_path($this->archivoJson));
            // Retorna el contenido del archivo como JSON
            return response()->json(json_decode($contenido, true));
        }
        // Si no existe, devuelve un array vacío
        return response()->json([]);
    }


    public function actualizarOAgregarArea(Request $request, $nombre_area)
    {
        // Ruta del archivo JSON
        $path = public_path($this->archivoJson);

        // Cargar el JSON actual
        $areas = json_decode(file_get_contents($path), true);

        // Verificar si el área ya existe
        if (isset($areas[$nombre_area])) {
            // Actualizar el área existente
            $areas[$nombre_area] = $request->input('area');
        } else {
            // Agregar una nueva área
            $areas[$nombre_area] = $request->input('area');
        }

        // Guardar el JSON actualizado
        file_put_contents($path, json_encode($areas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return response()->json(['message' => "Área '$nombre_area' actualizada correctamente."], 200);
    }

    public function eliminarArea($nombre_area)
    {
        // Ruta del archivo JSON
        $path = public_path($this->archivoJson);

        // Cargar el JSON actual
        $areas = json_decode(file_get_contents($path), true);

        // Verificar si el área existe
        if (isset($areas[$nombre_area])) {
            // Eliminar el área
            unset($areas[$nombre_area]);

            // Guardar el JSON actualizado
            file_put_contents($path, json_encode($areas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return response()->json(['message' => "Área '$nombre_area' eliminada correctamente."], 204);
        } else {
            return response()->json(['message' => "Área '$nombre_area' no encontrada."], 404);
        }
    }

    public function verificar($numero, $format)
    {
        $format_sds = FormatSds::where('id_sds', $format)->first();

        if (!$format_sds) {
            return response()->json([
                'es_actualizacion' => false
            ]);
        }

        // Traer todos los format_id asociados
        $formatIds = DB::table('formats_productivity_sds')
            ->where('format_sds_id', $format_sds->id)
            ->pluck('format_id'); // 👈 clave

        // Contar registros en receptions
        $count = Reception::where('file_number', $numero)
            ->whereIn('format_id', $formatIds)
            ->count();

        return response()->json([
            'es_actualizacion' => $count > 1 // 👈 true si hay más de uno
        ]);
    }
}
