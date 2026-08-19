<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Localidades;
use App\Models\User;

use App\Models\Format;
use App\Models\AdicionalFormat;

use App\Models\Entorno;

use App\Models\InterventionType;
use App\Models\Reception;
use App\Models\ConsecutivoPermission;
use Google\Service\CloudSearch\Resource\Query;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;

class ToolControlController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $localidades = Localidades::all();
        $entornos = Entorno::where('id', '!=', 0)->get();
        $format = Format::with('formatosAdicionales')->get();
        $intervenciones = InterventionType::all();
        $user_format = User::whereNot('environment_id', 0)->whereNotIn('role_id', [1, 7, 11, 12, 13])->get();

        return view('members.tools-control', compact('localidades', 'format', 'intervenciones', 'entornos', 'user_format'));
    }



    public function asigCons(Request $request)
    {
        $fechaIntervencion = \Carbon\Carbon::parse($request->fecha_intervencion);
        $inicioMesActual = now()->startOfMonth();
        $finMesActual = now()->endOfMonth();

        if (!$fechaIntervencion->between($inicioMesActual, $finMesActual)) {
            $inicioMesAnterior = now()->subMonthNoOverflow()->startOfMonth();
            $finMesAnterior = now()->subMonthNoOverflow()->endOfMonth();

            $tienePermisoVigente = $fechaIntervencion->between($inicioMesAnterior, $finMesAnterior)
                && ConsecutivoPermission::activeFor(Auth::id())->exists();

            if (!$tienePermisoVigente) {
                return back()
                    ->with('error', 'Solo puede registrar consecutivos con fecha de intervención del mes actual. Si necesita el mes anterior, solicite el permiso temporal.')
                    ->withInput();
            }
        }

        try {
            DB::beginTransaction();

            $localidadId = Localidades::where('identificador', $request->localidad)
                ->value('id');

            $localidad = strlen($request->localidad) === 1
                ? "0$request->localidad"
                : $request->localidad;

            $prefixConsecutivo = "1" . $localidad . "0" . $request->id_entorno;

            // 🔒 solo bloquea el último consecutivo del entorno
            $ultimo = Reception::where('environment', $request->id_entorno)
                ->select('consecutivo')
                ->orderByDesc('consecutivo')
                ->lockForUpdate()
                ->first();

            $base = $ultimo->consecutivo ?? 0;

            $rows = [];
            $ahora = now();

            for ($i = 1; $i <= $request->cantidad_consecutivos; $i++) {
                $cons = $base + $i;

                $rows[] = [
                    'environment' => $request->id_entorno,
                    'localidad_id' => $localidadId,
                    'required_date' => $ahora,
                    'consecutivo' => $cons,
                    'file_number' => $prefixConsecutivo . $cons,
                    'format_id' => $request->id_formato,
                    'intervention_type_id' => $request->id_intervencion,
                    'fecha_intervencion' => $request->fecha_intervencion,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ];
            }

            Reception::insert($rows);
            // Traer solo los insertados en esta transacción
            $nuevos = Reception::where('environment', $request->id_entorno)
                ->whereBetween('consecutivo', [
                    $base + 1,
                    $base + $request->cantidad_consecutivos
                ])
                ->get();

            $userId = Auth::id();
            foreach ($nuevos as $r) {
                $r->users()->attach($userId);
            }
            DB::commit();
            return back()->with('success', 'Consecutivos creados ✅');

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error("Error consecutivos: " . $e->getMessage());

            return back()->with('error', 'Error generando consecutivos ❌');
        }
    }



    public function getByEntorno($entorno_id)
    {
        $formatos = Format::whereNotIn('id', function ($query) {
            $query->select('id_format_adicional')->from('adicional_format');
        })
            ->where('environment_id', $entorno_id)
            ->where('is_active', 1)
            ->get();

        return response()->json($formatos);
    }

    public function getReceptions(Request $request)
{
    $userId = Auth::id();
    $search = $request->search;

    // 1️⃣ Traer solo IDs primero (consulta rápida)
    $idsQuery = Reception::whereHas('users', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });

    // 🔎 Filtro de búsqueda
    if ($search) {
        $idsQuery->where(function ($q) use ($search) {
            $q->where('file_number', 'like', "%$search%")
              ->orWhereHas('bases', function ($q2) use ($search) {
                  $q2->where('name', 'like', "%$search%");
              });
        });
    }

    $ids = $idsQuery
        ->orderByDesc('id')
        ->limit(50)
        ->pluck('id');

    // 2️⃣ Traer datos completos
    $receptions = Reception::with([
            'bases:id,name',
            'interventionType',
            'adicionalFormat:id,id_format,id_format_adicional',
            'users:id,name,last_name,email'
        ])
        ->whereIn('id', $ids)
        ->orderByDesc('id')
        ->get();

    // Relaciones adicionales
    $adicionales = DB::table('adicional_format')->get();

    $mapAdicionales = $adicionales->groupBy('id_format')
        ->map(fn($rows) => $rows->pluck('id_format_adicional')->toArray());

    $result = $receptions->groupBy('file_number')->map(function ($group) use ($mapAdicionales) {

        $final = [];

        foreach ($group as $reception) {

            $esHijo = collect($mapAdicionales)
                ->flatten()
                ->contains($reception->format_id);

            if ($esHijo) continue;

            $hijos = $group->whereIn(
                'format_id',
                $mapAdicionales[$reception->format_id] ?? []
            );

            $reception->children = $hijos->values();
            $final[] = $reception;
        }

        return $final;
    })->flatten(1)->values();

    return response()->json($result);
}



    public function saveNewFUserform(Request $request)
    {

        $reception = Reception::find($request->id);

        $userId = $request->id_User_new;

        if (!$reception->users()->where('user_id', $userId)->exists()) {
            $reception->users()->attach($userId);

        } else {
            return redirect()->back()->with('error', 'El usuario ya está asignado a este registro ❌');
        }

        return redirect()->back()->with('success', 'Usuario asignado correctamente ✅');
    }

    public function getFormatNames(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids)) {
            return response()->json(['error' => 'Invalid data'], 400);
        }

        $formats = Format::where('is_active', 1)
            ->whereIn('id', $ids)
            ->select('id', 'name')
            ->get();

        return response()->json($formats);
    }


    public function updateFormat(Request $request)
    {
        $id = $request->id_edit;
        $localidad_id = $request->localidad;

        $reception = Reception::find($id);
        $localidad = Localidades::find($localidad_id);

        if (!$reception) {
            return redirect()->back()->with('error', 'Registro no encontrado ❌');
        }

        if (!$localidad) {
            return redirect()->back()->with('error', 'Localidad no encontrada ❌');
        }

        $fileNumber = (string) $reception->file_number;
        $identificador = str_pad((string) $localidad->identificador, 2, '0', STR_PAD_LEFT);

        if (!empty($fileNumber) && strlen($fileNumber) >= 4) {
            $primer = substr($fileNumber, 0, 1);
            $prefijo = $identificador;
            $resto = substr($fileNumber, 3);
            $nuevoFileNumber = $primer . $prefijo . $resto;

            // ✅ Actualizar recepción padre
            $reception->update([
                'localidad_id' => $localidad_id,
                'file_number' => $nuevoFileNumber,
            ]);

            // ✅ Buscar hijos en la tabla adicional_format
            $hijos = DB::table('adicional_format')
                ->where('id_format', $reception->format_id)
                ->pluck('id_format_adicional');

            if ($hijos->isNotEmpty()) {
                // ✅ Buscar recepciones hijas con mismo file_number base
                $receptionsHijas = Reception::whereIn('format_id', $hijos)
                    ->where('file_number', $fileNumber) // mismas numeración antes del cambio
                    ->get();

                foreach ($receptionsHijas as $hija) {
                    // reconstruir file_number con nuevo prefijo
                    $fileNumberHija = (string) $hija->file_number;
                    if (!empty($fileNumberHija) && strlen($fileNumberHija) >= 4) {
                        $primerH = substr($fileNumberHija, 0, 1);
                        $restoH = substr($fileNumberHija, 3);
                        $nuevoFileNumberHija = $primerH . $prefijo . $restoH;

                        $hija->update([
                            'localidad_id' => $localidad_id,
                            'file_number' => $nuevoFileNumberHija,
                        ]);
                    }
                }
            }

        } else {
            return redirect()->back()->with('error', 'El campo file_number no es válido o muy corto ❌');
        }

        return redirect()->back()->with('success', 'Actualización realizada ✅');
    }

    public function updateFormatAdd(Request $request)
    {

        $id_number_format = $request->id_number_format;

        $reception = Reception::find($id_number_format);

        if (!$reception) {
            return redirect()->back()->with('error', 'Registro no encontrado ❌');
        }

        $reception->update([
            'quantity' => $request->cantidad_consecutivos_format,
        ]);


        return redirect()->back()->with('success', 'Actualización realizada ✅');
    }

    public function eliminarUsuarioAsignado($registroId, $userId)
    {
        try {
            $reception = Reception::find($registroId);
            if (!$reception) {
                return response()->json(['success' => false, 'message' => 'Registro no encontrado']);

            }
            $reception->users()->detach($userId);
            return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente']);
        } catch (\Exception $e) {
            Log::error("Error al eliminar usuario asignado: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el usuario',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function saveNewFormat(Request $request)
    {
        // Verificar si ya existe una recepción con el mismo file_number y format_id
        $exists = Reception::where('file_number', $request->file_number)
            ->where('format_id', $request->formato_nuevo)
            ->exists();

        if ($exists) {
            // Si ya existe, regresar con un mensaje de advertencia
            return redirect()->back()->with('error', 'Ya existe una formato adicional con el mismo número ❌');
        }

        // Crear nueva recepción
        $userIds = explode(',', $request->user_ids);

        $reception = Reception::create([
            'environment' => $request->environment,
            'localidad_id' => $request->localidad_id,
            'required_date' => now(),
            'consecutivo' => $request->consecutivo,
            'file_number' => $request->file_number,
            'format_id' => $request->formato_nuevo,
            'fecha_intervencion' => $request->fecha_intervencion,
            'quantity' => $request->cant_formato_nuevo
        ]);

        // Asociar todos los usuarios a la recepción
        $reception->users()->attach($userIds);

        // Redirigir con mensaje de éxito
        return redirect()->back()->with('success', 'Formato asignado correctamente ✅');
    }

}
