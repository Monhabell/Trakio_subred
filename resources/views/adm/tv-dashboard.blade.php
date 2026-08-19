<?php

namespace App\Http\Controllers;

use App\Models\Productivity;
use App\Models\Productivity_sds;
use App\Models\Reception;
use App\Models\ReceptionStatusHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TvDashboardController extends Controller
{
    public function index()
    {
        return view('adm.tv-dashboard');
    }

    public function data(): JsonResponse
    {
        $fichasSolicitadas = Reception::with(['bases', 'localidad', 'environment_file', 'users'])
            ->latest('id')
            ->take(12)
            ->get()
            ->map(fn (Reception $r) => [
                'id' => $r->id,
                'ficha' => $r->file_number,
                'formato' => optional($r->bases)->name ?? 'Formato sin nombre',
                'profesional' => $r->users->isNotEmpty() ? $this->fullName($r->users->first()) : null,
                'localidad' => optional($r->localidad)->name,
                'entorno' => optional($r->environment_file)->entorno,
                'cantidad' => $r->quantity,
                'fecha' => optional($r->created_at)->toIso8601String(),
            ]);

        $horasCargadas = Productivity::with(['user', 'receptions', 'base'])
            ->latest('id')
            ->take(12)
            ->get()
            ->map(function (Productivity $p) {
                $fileNumber = optional($p->receptions)->file_number;

                $segundos = $fileNumber
                    ? Productivity_sds::where('dig_id', $p->dig_id)
                        ->where('file_number', $fileNumber)
                        ->sum('calculation_time')
                    : 0;

                return [
                    'id' => $p->id,
                    'digitador' => $this->fullName($p->user),
                    'ficha' => $fileNumber,
                    'formato' => optional($p->base)->name,
                    'segundos' => (int) $segundos,
                    'fecha' => optional($p->created_at)->toIso8601String(),
                ];
            });

        $desdeCola = Carbon::now()->subMonth();

        // Fichas recibidas por GESI (status 11) que a&uacute;n no han sido digitadas.
        $pendientesDigitarQuery = fn () => Reception::where('status', 11)
            ->where('created_at', '>=', $desdeCola);

        $colaDigitacion = $pendientesDigitarQuery()
            ->with(['environment_file', 'bases'])
            ->withMin(['status_histories as recibido_gesi_at' => fn ($q) => $q->where('new_status', 11)], 'created_at')
            ->oldest('created_at')
            ->take(15)
            ->get()
            ->map(function (Reception $r) {
                $usuarios = $r->quantity_users ?: $r->quantity;
                $horas = $r->bases
                    ? $r->bases->time_header + ($r->bases->body_time * $usuarios)
                    : 0;
                $desde = $r->recibido_gesi_at ? Carbon::parse($r->recibido_gesi_at) : $r->created_at;

                return [
                    'id' => $r->id,
                    'ficha' => $r->file_number,
                    'entorno' => optional($r->environment_file)->entorno ?? 'Sin entorno',
                    'formato' => optional($r->bases)->name,
                    'horas' => round($horas, 2),
                    'espera_minutos' => $desde ? $desde->diffInMinutes(Carbon::now()) : null,
                ];
            });

        $colaPorEntorno = $pendientesDigitarQuery()
            ->with('environment_file')
            ->get()
            ->groupBy(fn (Reception $r) => optional($r->environment_file)->entorno ?? 'Sin entorno')
            ->map->count()
            ->map(fn ($cantidad, $entorno) => ['entorno' => $entorno, 'cantidad' => $cantidad])
            ->values();

        $inicioVentana = Carbon::now()->subHours(8)->second(0);
        $inicioVentana->minute($inicioVentana->minute < 30 ? 0 : 30);
        $bucketActual = (int) floor($inicioVentana->diffInMinutes(Carbon::now()) / 30);

        $conteosPorBucket = ReceptionStatusHistory::where('new_status', '4')
            ->where('created_at', '>=', $inicioVentana)
            ->selectRaw('FLOOR(TIMESTAMPDIFF(MINUTE, ?, created_at) / 30) as bucket, COUNT(*) as cantidad, COUNT(DISTINCT changed_by) as digitadores', [$inicioVentana])
            ->groupBy('bucket')
            ->get()
            ->keyBy('bucket');

        $produccionHoy = collect(range(0, $bucketActual))
            ->map(function (int $i) use ($inicioVentana, $conteosPorBucket) {
                $inicioBucket = $inicioVentana->copy()->addMinutes($i * 30);
                $bucket = $conteosPorBucket->get($i);

                return [
                    'hora' => $inicioBucket->format('H:i'),
                    'cantidad' => (int) ($bucket->cantidad ?? 0),
                    'digitadores' => (int) ($bucket->digitadores ?? 0),
                ];
            });

        $cambiosEstado = ReceptionStatusHistory::with(['user', 'reception'])
            ->latest('id')
            ->take(15)
            ->get()
            ->map(fn (ReceptionStatusHistory $h) => [
                'id' => $h->id,
                'ficha' => optional($h->reception)->file_number,
                'estado_anterior' => getStatusNameFile($h->previous_status),
                'estado_nuevo' => getStatusNameFile($h->new_status),
                'clase_nuevo' => getStatusFileClass($h->new_status),
                'usuario' => $h->user ? $this->fullName($h->user) : 'Sistema',
                'fecha' => optional($h->created_at)->toIso8601String(),
            ]);

        $usuariosEnLinea = $this->usuariosEnLinea();

        return response()->json([
            'server_time' => Carbon::now()->toIso8601String(),
            'fichas_solicitadas' => $fichasSolicitadas,
            'horas_cargadas' => $horasCargadas,
            'cola_digitacion' => $colaDigitacion,
            'cola_por_entorno' => $colaPorEntorno,
            'cambios_estado' => $cambiosEstado,
            'produccion_hoy' => $produccionHoy,
            'usuarios_en_linea' => $usuariosEnLinea,
            'contadores' => [
                'cola_digitacion' => $pendientesDigitarQuery()->count(),
                'usuarios_en_linea' => $usuariosEnLinea->count(),
            ],
        ]);
    }

    /**
     * Usuarios con sesión abierta (tabla `sessions`, driver database).
     * "En línea" = last_activity dentro de SESSION_LIFETIME, es decir, la misma
     * ventana que Laravel usa para considerar la sesión todavía válida (no cerrada,
     * no expirada). Una ventana más corta daría falsos "desconectados": last_activity
     * solo se actualiza con cada request, así que alguien puede llevar varios minutos
     * con la sesión abierta llenando un formulario sin generar tráfico nuevo.
     */
    private function usuariosEnLinea()
    {
        $umbral = Carbon::now()->subMinutes((int) config('session.lifetime'))->getTimestamp();

        return DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->whereNotNull('sessions.user_id')
            ->where('sessions.last_activity', '>=', $umbral)
            ->select('users.id', 'users.name', 'users.last_name', DB::raw('MAX(sessions.last_activity) as last_activity'))
            ->groupBy('users.id', 'users.name', 'users.last_name')
            ->orderByDesc('last_activity')
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'nombre' => trim($u->name . ' ' . $u->last_name),
                'ultima_actividad' => Carbon::createFromTimestamp($u->last_activity)->toIso8601String(),
            ]);
    }

    private function fullName($user): string
    {
        if (!$user) {
            return 'N/D';
        }

        return trim($user->name . ' ' . $user->last_name);
    }
}
