<?php

namespace App\Http\Controllers;

// Modelos
use App\Models\Consecutivo;
use App\Models\Reception;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Requests
use App\Http\Requests\StoreConsecutivosRequest;
use Illuminate\Support\Facades\DB;

class ConsecutivoController extends Controller
{
    public function index()
    {
        $consecutivos = Consecutivo::orderBy('created_at', 'desc')->limit(20)->get();
        $localidades = DB::table('localidades')->orderBy('identificador', 'ASC')->get();

        return view('dig.consecutivos', [
            'consecutivos' => $consecutivos,
            'localidades' => $localidades,
        ]);
    }

    public function filterByUser(Request $request)
    {
        // Verificar si se debe quitar el filtro
        if ($request->has('remove_filter')) {
            $aid = $request->input('user_id'); // Obtener el user_id para pasar a la redirección
            return redirect()->route('consecutivos.index', ['id' => $aid]); // Cambia esto a la ruta que desees
        }

        // Aplicar el filtro normalmente
        $userId = $request->input('user_id');

        // Obtener los consecutivos filtrados
        $consecutivos = Consecutivo::where('id_user', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Pasar el userId a la vista para saber si el filtro está activo
        return view('dig.consecutivos', compact('consecutivos', 'userId'));
    }

    public function show(Request $request)
    {
        // Validar el término de búsqueda
        $request->validate([
            'query' => 'required|string|max:255',
        ]);
        $localidades = DB::table('localidades')->orderBy('identificador', 'ASC')->get();

        // Obtener el término de búsqueda y dividirlo en palabras individuales
        $query = $request->input('query');
        $terms = explode(' ', $query);

        // Buscar en los campos deseados asegurando que todos los términos coincidan
        $consecutivos = Consecutivo::where(function ($queryBuilder) use ($terms) {
            foreach ($terms as $term) {
                $queryBuilder->where(function ($subQuery) use ($term) {
                    $subQuery->where('consecutivo', 'LIKE', "%{$term}%")
                        ->orWhere('primer_nombre', 'LIKE', "%{$term}%")
                        ->orWhere('segundo_nombre', 'LIKE', "%{$term}%")
                        ->orWhere('primer_apellido', 'LIKE', "%{$term}%")
                        ->orWhere('id_ficha', 'LIKE', "%{$term}%");
                });
            }
        })
            // Buscar también en la tabla 'receptions' relacionada, asegurando que todos los términos coincidan
            ->orWhereHas('receptions', function ($queryBuilder) use ($terms) {
                foreach ($terms as $term) {
                    $queryBuilder->where('file_number', 'LIKE', "%{$term}%");
                }
            })
            ->get();

        // Pasar los resultados a la vista
        return view('dig.consecutivos', [
            'consecutivos' => $consecutivos,
            'localidades' => $localidades
        ]);
    }


    public function store(StoreConsecutivosRequest $request)
    {
        try {
            $user_id = Auth::id();
            $consultaFicha = Reception::where('file_number', $request->ficha)->first();

            if (!$consultaFicha) {
                return back()->with('error', 'La ficha no está en recepciones');
            }

            // Convertir nombres y apellidos a formato de "Nombre Propio"
            $primer_nombre = ucwords(strtolower($request->primer_nombre));
            $segundo_nombre = ucwords(strtolower($request->segundo_nombre));
            $primer_apellido = ucwords(strtolower($request->primer_apellido));
            $segundo_apellido = ucwords(strtolower($request->segundo_apellido));
            $upz = $request->upz;

            $existeConsecutivoCompleto = Consecutivo::where('primer_nombre', $primer_nombre)
                ->where('primer_apellido', $primer_apellido)
                ->where('segundo_nombre', $segundo_nombre)
                ->where('segundo_apellido', $segundo_apellido)
                ->where('up7', $upz)
                ->where('localidad', $request->localidad)
                ->exists();

            // Verificar coincidencia parcial
            $existeConsecutivoParcial = Consecutivo::where('primer_nombre', $primer_nombre)
                ->where('primer_apellido', $primer_apellido)
                ->where('up7', $upz)
                ->where('localidad', $request->localidad)
                ->exists();

            // Retornar error si se encuentra coincidencia en cualquiera de las dos consultas
            if ($existeConsecutivoCompleto || $existeConsecutivoParcial) {
                return back()->with('error', 'A este usuario ya se le asignó un consecutivo.')->withInput();
            }

            // Crear el registro sin el consecutivo primero
            try {
                $consecutivo = Consecutivo::create([
                    'id_ficha' => $consultaFicha->id,
                    'localidad' => $request->localidad,
                    'up7' => $upz,
                    'primer_nombre' => $primer_nombre,
                    'segundo_nombre' => $segundo_nombre,
                    'primer_apellido' => $primer_apellido,
                    'segundo_apellido' => $segundo_apellido,
                    'id_user' => $user_id,
                ]);
            } catch (\Exception $e) {
                dd($e->getMessage());
                return back()->with('error', 'Error al guardar el consecutivo.');
            }

            // Generar el consecutivo concatenando los campos
            $primer_nombre_letra = substr($primer_nombre, 0, 1);
            $segundo_nombre_letra = substr($segundo_nombre, 0, 1);
            $primer_apellido_letra = substr($primer_apellido, 0, 1);
            $segundo_apellido_letra = substr($segundo_apellido, 0, 1);

            // Si no hay segundo nombre o segundo apellido, usar una letra por defecto
            $segundo_nombre_letra = $segundo_nombre_letra ?: '';
            $segundo_apellido_letra = $segundo_apellido_letra ?: '';

            // Concatenar para generar el consecutivo
            $codigo_consecutivo = $request->localidad . $upz .
                strtoupper($primer_nombre_letra) .
                strtoupper($segundo_nombre_letra) .
                strtoupper($primer_apellido_letra) .
                strtoupper($segundo_apellido_letra) .
                $consecutivo->id;

            // Actualizar el registro con el consecutivo generado
            $consecutivo->update(['consecutivo' => $codigo_consecutivo]);

            // Redireccionar con éxito
            return redirect()->back()->with('success', 'Consecutivo generado: ' . $codigo_consecutivo);
        } catch (\Exception $e) {
            dd($e); // Captura cualquier excepción y muestra el error
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}