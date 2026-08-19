<?php

namespace App\Http\Controllers;

// Modelos
use App\Models\Document;
use App\Models\Reception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Localidades;
use App\Models\Entorno;
use App\Models\Productivity;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DigitadoresExport;
use App\Models\DataUser;
use Carbon\Carbon;
use App\Models\Productivity_sds;
use App\Models\OtherTime;
use App\Models\License;
use App\Mail\GesiDeliveryReminderMail;
use App\Console\Commands\GesiDeliveryReminder as GesiDeliveryReminderCommand;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;



// Facades
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $userAuth = Auth::user();
        $environment_user_id = $userAuth->environment_id;


        // Lógica de redirección basada en el rol y entorno del usuario
        switch ($environment_user_id) {
            case 0:
                if ($userAuth->subnet_id != 1) {
                    return $this->guestHome();
                }

                if ($userAuth->is_admin) {
                    return $this->adminHome($request);
                } else {
                    return $this->digitizersHome();
                }
                break;
            case 2:
            case 3:
            case 4:
            case 6:
                if ($userAuth->is_admin) {
                    return $this->environmentHome();
                } else {
                    return $this->membersHome($request);
                }
                break;
            default:
                // Redirigir a una ruta por defecto si no coincide
                return redirect()->route('home');
                break;
        }
    }

    public function sendGesiReminder(Request $request)
    {
        $deadlineDate = GesiDeliveryReminderCommand::getSecondBusinessDayNextMonth();
        $deadlineDateFormatted = $deadlineDate->translatedFormat('l j \d\e F \d\e Y');
        $professionalDeadlineDateFormatted = GesiDeliveryReminderCommand::getLastDayOfCurrentMonth()->translatedFormat('l j \d\e F \d\e Y');
        $currentMonthName = now()->translatedFormat('F Y');

        $pendingFichas = $this->buildPendingFichas();
        $fichasByUser  = $pendingFichas['by_user'];
        $fichasByCoord = $pendingFichas['by_coordinator'];

        $users = User::where('is_active', true)
            ->where(function ($q) {
                $q->where('environment_id', '!=', 0)
                  ->orWhere('is_admin', true);
            })
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        $totalPendingCount = Reception::whereIn('status', [1, 2])
            ->whereNotNull('fecha_intervencion')
            ->whereMonth('fecha_intervencion', now()->month)
            ->whereYear('fecha_intervencion', now()->year)
            ->count();

        $sent = 0;

        foreach ($users as $user) {
            $isGesiAdmin = $user->environment_id == 0 && $user->is_admin;
            $isEnvAdmin  = $user->environment_id != 0 && $user->is_admin;

            if ($isGesiAdmin) {
                $fichas = [];
            } elseif ($isEnvAdmin) {
                $fichas = $fichasByCoord[$user->id] ?? []; // fichas de los profesionales que aprobó
            } else {
                $fichas = $fichasByUser[$user->id] ?? []; // fichas propias
            }
            $fullName = trim($user->name . ' ' . $user->last_name);

            try {
                Mail::to($user->email)->send(new GesiDeliveryReminderMail(
                    userName: $fullName,
                    deadlineDate: $deadlineDateFormatted,
                    currentMonthName: $currentMonthName,
                    delayedFichas: $fichas,
                    isEnvironmentAdmin: $isEnvAdmin,
                    isGesiAdmin: $isGesiAdmin,
                    totalPendingCount: $isGesiAdmin ? $totalPendingCount : 0,
                    professionalDeadlineDate: $professionalDeadlineDateFormatted,
                ));
                $sent++;
            } catch (\Exception $e) {
                Log::error("GesiDeliveryReminder: fallo al enviar a {$user->email} — " . $e->getMessage());
            }
        }

        Cache::put(
            'gesi_reminder_sent_' . now()->format('Y-m'),
            ['sent_at' => now()->format('d/m/Y H:i'), 'count' => $sent],
            now()->addDays(45)
        );

        return redirect()->route('app.home')
            ->with('gesi_reminder_success', "Recordatorio enviado a {$sent} usuario(s). Fecha límite: {$deadlineDateFormatted}.");
    }

    private function buildPendingFichas(): array
    {
        $today = now()->startOfDay();

        // Mapeo coordinador → profesionales que aprobó (misma lógica que reception-delay-alerts)
        // receptions.delivered_by = quien aprobó la recepción (el coordinador)
        $coordToProfIds = DB::table('reception_user')
            ->join('receptions', 'receptions.id', '=', 'reception_user.reception_id')
            ->whereNotNull('receptions.delivered_by')
            ->select('receptions.delivered_by as coord_id', 'reception_user.user_id as prof_id')
            ->get()
            ->groupBy('coord_id')
            ->map(fn($rows) => $rows->pluck('prof_id')->unique()->values()->toArray());

        $receptions = Reception::with([
            'environment_file:id,entorno',
            'bases:id,name',
            'users' => fn($q) => $q->select('users.id', 'users.name', 'users.last_name')->distinct(),
        ])
        ->select(['id', 'file_number', 'fecha_intervencion', 'status', 'environment', 'format_id'])
        ->where('status', 1)
        ->whereNotNull('fecha_intervencion')
        ->whereMonth('fecha_intervencion', now()->month)
        ->whereYear('fecha_intervencion', now()->year)
        ->get();

        $byUserId  = []; // profesionales: sus propias fichas
        $byCoordId = []; // coordinadores: fichas de sus profesionales aprobados

        // Mapa inverso: profId => [coordIds] — evita el in_array en el loop interno
        // convirtiendo O(receptions * users * coords) en O(receptions * users)
        $profToCoordIds = [];
        foreach ($coordToProfIds as $coordId => $profIds) {
            foreach ($profIds as $profId) {
                $profToCoordIds[$profId][] = $coordId;
            }
        }

        foreach ($receptions as $reception) {
            $daysDelay = Carbon::parse($reception->fecha_intervencion)->diffInDays($today);

            $row = [
                'file_number'        => $reception->file_number,
                'environment'        => $reception->environment_file->entorno ?? 'Sin entorno',
                'format'             => $reception->bases->name ?? 'Sin formato',
                'fecha_intervencion' => Carbon::parse($reception->fecha_intervencion)->format('d/m/Y'),
                'days_delay'         => $daysDelay,
                'professional'       => '',
            ];

            foreach ($reception->users as $user) {
                $byUserId[$user->id][] = $row;

                foreach ($profToCoordIds[$user->id] ?? [] as $coordId) {
                    $byCoordId[$coordId][] = array_merge($row, [
                        'professional' => trim($user->name . ' ' . $user->last_name),
                    ]);
                }
            }
        }

        return ['by_user' => $byUserId, 'by_coordinator' => $byCoordId];
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function adminHome(Request $request)
    {
        /* =========================
         * 1️⃣ RANGO DE FECHAS menos un mes
         * ========================= */
        // $inicioMes = Carbon::now()
        //     ->subMonth()
        //     ->startOfMonth()
        //     ->toDateString();

        // $finMes = Carbon::now()
        //     ->subMonth()
        //     ->endOfMonth()
        //     ->toDateString();

        $inicioMes = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->toDateString());
        $finMes = $request->input('fecha_fin', Carbon::now()->endOfMonth()->toDateString());


        $baseQuery = Reception::whereBetween('fecha_intervencion', [$inicioMes, $finMes]);

        /* =========================
         * 2️⃣ PIPELINE
         * ========================= */
        $flujo = [
            'Solicitados' => [1, 2, 3, 11, 12, 4, 13, 5, 14, 6, 7],
            'Rechazados' => [10],
            'Sellados' => [2, 3, 11, 12, 4, 13, 5, 14, 6, 7],
            'Pasados GESI' => [11, 12, 4, 13, 5, 14, 6, 7],
            'Digitados' => [4, 13, 5, 14, 6, 7],
            'Devueltos' => [5, 14, 6, 7],
        ];

        // Una sola consulta con CASE WHEN reemplaza los 6 COUNTs separados del pipeline
        // más los 2 COUNTs de KPI (enTramite y devueltosFinalizados)
        $countsRaw = (clone $baseQuery)
            ->selectRaw("
                SUM(CASE WHEN status IN (1,2,3,11,12,4,13,5,14,6,7) THEN 1 ELSE 0 END) as solicitados,
                SUM(CASE WHEN status IN (10)                          THEN 1 ELSE 0 END) as rechazados,
                SUM(CASE WHEN status IN (2,3,11,12,4,13,5,14,6,7)    THEN 1 ELSE 0 END) as sellados,
                SUM(CASE WHEN status IN (11,12,4,13,5,14,6,7)         THEN 1 ELSE 0 END) as pasados_gesi,
                SUM(CASE WHEN status IN (4,13,5,14,6,7)               THEN 1 ELSE 0 END) as digitados,
                SUM(CASE WHEN status IN (5,14,6,7)                    THEN 1 ELSE 0 END) as devueltos,
                SUM(CASE WHEN status IN (11,12,4,13)                  THEN 1 ELSE 0 END) as en_tramite
            ")
            ->first();

        $grafica = [
            'Solicitados'  => (int) ($countsRaw->solicitados  ?? 0),
            'Rechazados'   => (int) ($countsRaw->rechazados   ?? 0),
            'Sellados'     => (int) ($countsRaw->sellados     ?? 0),
            'Pasados GESI' => (int) ($countsRaw->pasados_gesi ?? 0),
            'Digitados'    => (int) ($countsRaw->digitados    ?? 0),
            'Devueltos'    => (int) ($countsRaw->devueltos    ?? 0),
        ];

        $consecutivosTotal = $grafica['Solicitados'];

        // Fichas recibidas por GESI (status 11) pendientes por digitar, agrupadas por entorno
        // (mismo cálculo usado en el home del digitador). Incluye el desglose actualización/nuevas.
        // Se respeta el rango de fechas filtrado ($inicioMes/$finMes) para que coincida con el resto del pipeline.
        $pendientesPorEntorno = $this->getPendientesPorDigitar($inicioMes, $finMes);
        $totalPendientesDigitar = $pendientesPorEntorno->sum('total');
        $totalVencidasDigitar = $pendientesPorEntorno->sum('vencidas');
        $totalActualizacionDigitar = $pendientesPorEntorno->sum('actualizacion');
        $totalNuevasDigitar = $pendientesPorEntorno->sum('nuevas');

        // Datos del pipeline ya organizados en el orden del flujo real (Solicitados → Devueltos),
        // desglosando el bloque "Pendientes por Digitar" en Actualización vs Nuevas para que se
        // entienda de un vistazo cuánto de lo pendiente es reingreso y cuánto es ficha nueva.
        $pipelineLabels = [
            'Solicitados',
            'Rechazados',
            'Sellados',
            'En GESI',
            'Pendientes: Actualización',
            'Pendientes: Nuevas',
            'Digitados',
            'Devueltos',
        ];
        $pipelineData = [
            $grafica['Solicitados'],
            $grafica['Rechazados'],
            $grafica['Sellados'],
            $grafica['Pasados GESI'],
            $totalActualizacionDigitar,
            $totalNuevasDigitar,
            $grafica['Digitados'],
            $grafica['Devueltos'],
        ];

        /* =========================
         * 3️⃣ KPI (ya calculados en la consulta CASE WHEN de arriba)
         * ========================= */
        $enTramite            = (int) ($countsRaw->en_tramite ?? 0);
        $devueltosFinalizados = (int) ($countsRaw->devueltos  ?? 0);

        /* =========================
         * 4️⃣ DEMANDA POR ENTORNO
         * ========================= */
        $demandaEntorno = (clone $baseQuery)
            ->selectRaw('environment, COUNT(*) as total')
            ->groupBy('environment')
            ->pluck('total', 'environment');

        /* =========================
         * 5️⃣ ALERTAS de retraso por entorno (fast: CASE WHEN GROUP BY)
         * ========================= */
        $alertasLocalidad = (clone $baseQuery)
            ->selectRaw("
                environment,
                COUNT(*) as total,
                SUM(CASE WHEN status IN (4,13,5,14,6,7) THEN 1 ELSE 0 END) as digitados,
                COUNT(*) - SUM(CASE WHEN status IN (4,13,5,14,6,7) THEN 1 ELSE 0 END) as pendientes
            ")
            ->groupBy('environment')
            ->orderByDesc('pendientes')
            ->get();

        /* =========================
         * PRODUCTIVIDAD — se carga vía AJAX en adminHomeHeavy()
         * ========================= */
        $hoy = Carbon::now();

        // Las secciones pesadas (productividad, calidad, formatos) se cargan
        // vía adminHomeHeavy() para que la página aparezca de inmediato

        // Una sola consulta GROUP BY reemplaza 4 COUNTs separados (uno por mes)
        $historicoCounts = Reception::selectRaw('YEAR(fecha_intervencion) as yr, MONTH(fecha_intervencion) as mo, COUNT(*) as total')
            ->whereIn('status', [4, 13, 5, 14, 6, 7])
            ->whereBetween('fecha_intervencion', [
                Carbon::now()->subMonths(3)->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->groupByRaw('YEAR(fecha_intervencion), MONTH(fecha_intervencion)')
            ->get()
            ->keyBy(fn($r) => $r->yr . '-' . str_pad($r->mo, 2, '0', STR_PAD_LEFT));

        $historico = [];
        for ($i = 3; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $key   = $fecha->format('Y-m');
            $historico['labels'][] = $fecha->translatedFormat('F');
            $historico['data'][]   = (int) ($historicoCounts->get($key)?->total ?? 0);
        }

        /* =========================
        * 7️⃣ ALERTAS DE RETRASO CRÍTICO
        * ========================= */

        $hoy = Carbon::now();
        $inicioMesActual = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->toDateString());
        $finMesActual = $request->input('fecha_fin', Carbon::now()->endOfMonth()->toDateString());


        $estatusNoDigitados = [1, 2];

        // El filtro diffInDays > 5 y el agrupamiento se hacen en SQL para evitar
        // cargar todas las filas en PHP con ->get() y filtrarlas en memoria
        $alertasCriticasEntorno = Reception::join('environments', 'receptions.environment', '=', 'environments.id')
            ->select('environments.entorno as nombre_entorno', DB::raw('COUNT(*) as cantidad'))
            ->whereBetween('receptions.fecha_intervencion', [$inicioMesActual, $finMesActual])
            ->whereIn('receptions.status', $estatusNoDigitados)
            ->whereRaw('ABS(DATEDIFF(CURDATE(), receptions.fecha_intervencion)) > 5')
            ->groupBy('environments.entorno')
            ->get()
            ->map(fn($item) => [
                'entorno'  => $item->nombre_entorno,
                'cantidad' => (int) $item->cantidad,
            ])->values();

        // Los labels ahora serán nombres: "Norte", "Sur", "Occidente", etc.
        $labelsEntornosCriticos = $alertasCriticasEntorno->pluck('entorno');
        $dataCriticaEntorno = $alertasCriticasEntorno->pluck('cantidad');



        $emailNotification = Cache::get('gesi_reminder_sent_' . now()->format('Y-m'));

        return view('adm.home', [
            'emailNotification'        => $emailNotification,
            'grafica'                  => $grafica,
            'consecutivosTotal'        => $consecutivosTotal,
            'enTramite'                => $enTramite,
            'devueltosFinalizados'     => $devueltosFinalizados,
            'demandaEntorno'           => $demandaEntorno,
            'alertasLocalidad'         => $alertasLocalidad,
            'inicioMes'                => $inicioMes,
            'finMes'                   => $finMes,
            'historico'                => $historico,
            'labelsEntornosCriticos'   => $labelsEntornosCriticos,
            'dataCriticaEntorno'       => $dataCriticaEntorno,
            'alertasCriticasEntorno'   => $alertasCriticasEntorno,
            'pipelineLabels'           => $pipelineLabels,
            'pipelineData'             => $pipelineData,
            'pendientesPorEntorno'     => $pendientesPorEntorno,
            'totalPendientesDigitar'   => $totalPendientesDigitar,
            'totalVencidasDigitar'     => $totalVencidasDigitar,
            'totalActualizacionDigitar'=> $totalActualizacionDigitar,
            'totalNuevasDigitar'       => $totalNuevasDigitar,
            // Las siguientes se cargan vía AJAX (adminHomeHeavy)
            'calidadEntorno'           => [],
            'reporteFormatos'          => collect(),
        ]);
    }

    /**
     * Endpoint AJAX: carga las secciones pesadas del dashboard admin
     * (productividad, calidad por entorno, auditoría de formatos).
     * Se llama desde JS después de que la página ya renderizó.
     */
    public function adminHomeHeavy(Request $request)
    {
        $inicioMesP = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->toDateString());
        $finMesP    = $request->input('fecha_fin',    Carbon::now()->endOfMonth()->toDateString());

        /* 1️⃣ PRODUCTIVIDAD */
        $otherTimesSub = DB::table('other_times')
            ->selectRaw('id_user, SUM(time * 60) as total_ot_minutes')
            ->where('approved', 1)
            ->whereBetween('date_time', [$inicioMesP, $finMesP])
            ->groupBy('id_user');

        $productividad = Productivity::withoutGlobalScopes()
            ->from('productivity as p')
            ->whereBetween('p.created_at', [$inicioMesP, $finMesP])
            ->join('formats as f', 'f.id', '=', 'p.base_id')
            ->join('users as u', 'u.id', '=', 'p.dig_id')
            ->join('data_users as du', 'du.id_user', '=', 'u.id')
            ->leftJoinSub($otherTimesSub, 'ot', fn($j) => $j->on('ot.id_user', '=', 'u.id'))
            ->selectRaw('
                u.id as user_id,
                CONCAT(SUBSTRING_INDEX(u.name," ",1)," ",SUBSTRING_INDEX(u.last_name," ",1)) as digitador,
                du.environment_assignment_id as entorno,
                (SUM(f.body_time*p.quantity_users)+SUM(f.header_time)+COALESCE(ot.total_ot_minutes,0)) as total_productividad
            ')
            ->groupBy('u.id','u.name','u.last_name','du.environment_assignment_id','ot.total_ot_minutes')
            ->orderByDesc('total_productividad')
            ->get()
            ->map(function ($item) {
                $metaMensual = 8 * 60 * 23;
                return [
                    'digitador'   => $item->digitador,
                    'entorno'     => $item->entorno,
                    'total_min'   => round($item->total_productividad, 2),
                    'total_horas' => round($item->total_productividad / 60, 2),
                    'porcentaje'  => min(100, round(($item->total_productividad / $metaMensual) * 100, 1)),
                ];
            });

        $dataEntornos = $productividad
            ->groupBy('entorno')
            ->map(fn($items) => round($items->sum('total_horas'), 2));

        /* 2️⃣ CALIDAD POR ENTORNO */
        $calidadEntorno = Productivity::withoutGlobalScopes()
            ->join('users as u', 'u.id', '=', 'productivity.dig_id')
            ->join('data_users as du', 'du.id_user', '=', 'u.id')
            ->selectRaw("
                du.environment_assignment_id as entorno,
                COUNT(*) as total_registros,
                SUM(CASE WHEN productivity.observations IS NOT NULL AND productivity.observations != '' THEN 1 ELSE 0 END) as total_errores
            ")
            ->whereBetween('productivity.created_at', [$inicioMesP, $finMesP])
            ->groupBy('du.environment_assignment_id')
            ->get()
            ->map(function ($item) {
                $pct = $item->total_registros > 0
                    ? round(($item->total_errores / $item->total_registros) * 100, 2) : 0;
                return [
                    'entorno'         => $item->entorno,
                    'total'           => $item->total_registros,
                    'errores'         => $item->total_errores,
                    'porcentaje_error'=> $pct,
                    'semaforo'        => $pct > 10 ? 'danger' : ($pct > 5 ? 'warning' : 'success'),
                ];
            });

        /* 3️⃣ AUDITORÍA DE FORMATOS */
        $reporteFormatos = DB::table('productivity_calculation as pc')
            ->leftJoin('formats_sds as fs', 'pc.sds_id', '=', 'fs.id_sds')
            ->leftJoin('formats_productivity_sds as pivot', fn($j) => $j->on('pivot.format_sds_id', '=', 'fs.id'))
            ->leftJoin('formats as f', 'pivot.format_id', '=', 'f.id')
            ->leftJoin('receptions as r', 'pc.file_number', '=', 'r.file_number')
            ->select(
                'f.name as nombre_formato', 'f.header_time', 'f.body_time',
                DB::raw('AVG(pc.calculation_time / 60) as tiempo_real_individual'),
                DB::raw('AVG((IFNULL(r.quantity_users, r.quantity) * f.body_time) + f.header_time) as tiempo_esperado_individual'),
                DB::raw('AVG(IFNULL(pc.pages, 0)) as paginas_promedio'),
                DB::raw('AVG(IFNULL(r.quantity_users, r.quantity)) as usuarios_promedio_receptions'),
                DB::raw('AVG(IFNULL(r.quantity_users, 0)) as promedio_usuarios'),
                DB::raw('COUNT(pc.id) as total_procesado')
            )
            ->where('pc.created_at', '>=', '2026-03-15 00:00:00')
            ->whereNotNull('f.name')
            ->groupBy('f.name', 'f.header_time', 'f.body_time')
            ->get();

        return response()->json([
            'labelsDigitadores' => $productividad->pluck('digitador')->values(),
            'dataPorcentajes'   => $productividad->pluck('porcentaje')->values(),
            'dataEntornos_keys' => $dataEntornos->keys()->values(),
            'dataEntornos_vals' => $dataEntornos->values()->values(),
            'calidad_html'      => view('adm.partials.calidad-bars', compact('calidadEntorno'))->render(),
            'formatos_html'     => view('adm.partials.formatos-rows', compact('reporteFormatos'))->render(),
        ]);
    }

    /**
     * Muestra la vista del dashboard de Entorno y proporciona las listas de filtros disponibles.
     */
    public function environmentHome()
    {
        // Obtener Entornos y Localidades disponibles para los filtros (Asumiendo que Entorno y Localidades son Modelos)
        $availableEntornos = Entorno::whereIn(
            'id',
            Reception::distinct()->pluck('environment')->filter()
        )->select('id', 'entorno')->get();

        $availableLocalidades = Localidades::whereIn(
            'id',
            Reception::distinct()->pluck('localidad_id')->filter()
        )->select('id', 'name')->get();

        // Obtener Meses disponibles (ej. últimos 6 meses)
        $availableMonths = [];
        $start = Carbon::now()->subMonths(5)->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        while ($start->lte($end)) {
            $availableMonths[] = [
                'value' => $start->format('Y-m'),
                'label' => $start->isoFormat('MMM YYYY')
            ];
            $start->addMonth();
        }

        $currentMonth = Carbon::now()->format('Y-m');



        return view('env.home', [
            'availableEntornos' => $availableEntornos,
            'availableLocalidades' => $availableLocalidades,
            'availableMonths' => array_reverse($availableMonths),
            'currentMonth' => $currentMonth,

        ]);
    }

    /**
     * Endpoint API para cargar las métricas filtradas vía AJAX.
     */
    public function getFilteredMetrics(Request $request)
    {
        // 1. Obtener los parámetros de filtro


        $entorno = $request->get('entorno');
        $localidad = $request->get('localidad');
        $mes = $request->get('mes');

        // 2. Definir el rango de tiempo
        if ($mes) {
            $start_date = Carbon::parse($mes)->startOfMonth();
            $end_date = Carbon::parse($mes)->endOfMonth();
        } else {
            $start_date = Carbon::now()->subMonths(4)->startOfMonth();
            $end_date = Carbon::now()->endOfMonth();
        }

        // 3. Consulta base (para métricas generales y flujo)
        $recordsQuery = DB::table('receptions')
            ->leftJoin('productivity', 'productivity.file_id', '=', 'receptions.id')
            ->select(
                'receptions.id',
                'receptions.environment as entorno',
                'receptions.localidad_id as localidad',
                'receptions.stamp_date as fecha_solicitud',
                'receptions.return_date as fecha_retorno_entorno',
                'receptions.fecha_intervencion',
                'receptions.package_id',
                'productivity.observations as observaciones',

                // Fecha en que GESI recibió la ficha (status 11)
                DB::raw('(SELECT MIN(rsh.created_at) FROM reception_status_histories rsh WHERE rsh.reception_id = receptions.id AND rsh.new_status = 11) as fecha_recibido_gesi'),
                // Fecha en que fue devuelta al entorno (status 5)
                DB::raw('(SELECT MIN(rsh.created_at) FROM reception_status_histories rsh WHERE rsh.reception_id = receptions.id AND rsh.new_status = 5) as fecha_devuelta_entorno'),

                // Métrica: Ficha en proceso GESI (Ya tiene paquete, no ha regresado)
                DB::raw('CASE WHEN receptions.package_id IS NOT NULL AND receptions.return_date IS NULL THEN 1 ELSE 0 END as pendiente_gesi'),
                // Métrica: Ficha Pendiente de Ingreso a GESI (No tiene paquete, no ha regresado)
                DB::raw('CASE WHEN receptions.package_id IS NULL AND receptions.return_date IS NULL AND receptions.status != 10 THEN 1 ELSE 0 END as pendiente_ingreso_gesi'),
                // Métrica: Tiene Observaciones
                DB::raw('CASE WHEN productivity.observations IS NOT NULL AND productivity.observations != "" THEN 1 ELSE 0 END as tiene_observacion')
            )
            ->whereBetween('receptions.fecha_intervencion', [$start_date, $end_date]);

        // 4. Aplicar filtros dinámicos
        if ($entorno && $entorno !== 'TODOS') {
            $recordsQuery->where('receptions.environment', $entorno);
        }

        if ($localidad && $localidad !== 'TODOS') {
            $recordsQuery->where('receptions.localidad_id', $localidad);
        }

        // 5. Ejecutar la consulta de métricas generales
        $records = $recordsQuery->get();

        // 6. Procesar las métricas generales y flujo
        $dataMetrics = $this->calculateAllMetrics($records);

        // ===========================================================
        // 🔹 SECCIÓN: Usuarios solicitantes (Top 10 Detallado vía SQL)
        // ===========================================================
        $topUsuariosQuery = DB::table('reception_user')
            ->join('users', 'users.id', '=', 'reception_user.user_id')
            ->join('receptions', 'receptions.id', '=', 'reception_user.reception_id')
            ->leftJoin('productivity', 'productivity.file_id', '=', 'receptions.id')
            ->whereBetween('receptions.fecha_intervencion', [$start_date, $end_date]);

        // Aplicar filtros a la consulta del Top 10
        if ($entorno && $entorno !== 'TODOS') {
            $topUsuariosQuery->where('receptions.environment', $entorno);
        }

        if ($localidad && $localidad !== 'TODOS') {
            $topUsuariosQuery->where('receptions.localidad_id', $localidad);
        }

        // Agrupamos por usuario y calculamos las métricas
        $topUsuarios = $topUsuariosQuery
            ->select(
                'users.name',
                DB::raw('COUNT(reception_user.reception_id) as asignados'),
                DB::raw('COUNT(CASE WHEN receptions.return_date IS NOT NULL THEN 1 END) as retornados'),
                DB::raw('COUNT(CASE WHEN receptions.return_date IS NOT NULL AND productivity.observations IS NOT NULL AND productivity.observations != "" THEN 1 END) as observaciones')
            )
            ->groupBy('users.name')
            ->orderByDesc('observaciones')

            ->get();

        // Procesar el resultado para el formato de la tabla
        $topUsersData = $topUsuarios->map(function ($user) {
            $tasaError = ($user->retornados > 0) ? ($user->observaciones / $user->retornados) * 100 : 0;
            // Cálculo de Pendientes Simplificado: Asignados totales - Retornados
            $pendientes_total = (int) $user->asignados - (int) $user->retornados;

            return [
                'id' => $user->name,
                'nombre' => $user->name,
                'asignados' => (int) $user->asignados,
                'retornados' => (int) $user->retornados,
                'observaciones' => (int) $user->observaciones,
                'tasa_error' => number_format($tasaError, 1) . '%',
                'pendientes_total' => $pendientes_total,
            ];
        })->toArray();


        $dataMetrics['topUsuarios'] = [
            'title' => 'Usuarios Solicitantes',
            'data' => $topUsersData,
        ];

        return response()->json($dataMetrics);
    }

    /**
     * Calcula todas las métricas para un conjunto de registros.
     */
    private function calculateAllMetrics($records)
    {
        if ($records->isEmpty()) {
            return [
                'totalAsignados' => 0,
                'pendientesGesi' => 0,
                'pendientesIngresoGesi' => 0,
                'tiempoPromedioCiclo' => 0,
                'tasaError' => 0,
                'flujoMensual' => ['meses' => [], 'solicitados' => [], 'retornados' => []],
                'distribucionObservaciones' => ['tipos' => [], 'conteo' => []],
                'topUsuarios' => ['title' => 'Usuarios Solicitantes', 'data' => []],
            ];
        }


        $totalAsignados = $records->count();
        // Métricas de Pendientes
        $pendientesGesi = $records->where('pendiente_gesi', 1)->count();
        $pendientesIngresoGesi = $records->where('pendiente_ingreso_gesi', 1)->count();

        $retornados = $records->where('fecha_retorno_entorno', '!=', null);
        $totalRetornados = $retornados->count();


        // Tiempo Promedio de Ciclo: desde que GESI recibió (status 11) hasta que fue devuelta al entorno (status 5)
        $conCicloCompleto = $records->filter(fn($r) => !empty($r->fecha_recibido_gesi) && !empty($r->fecha_devuelta_entorno));
        $totalConCiclo = $conCicloCompleto->count();
        $tiempoCicloTotal = $conCicloCompleto->sum(function ($r) {
            $recibido = Carbon::parse($r->fecha_recibido_gesi);
            $devuelta = Carbon::parse($r->fecha_devuelta_entorno);
            return max(0, $recibido->diffInDays($devuelta, true));
        });
        $tiempoPromedioCiclo = ($totalConCiclo > 0) ? $tiempoCicloTotal / $totalConCiclo : 0;

        // Calidad / Tasa de Error
        $conObservaciones = $retornados->where('tiene_observacion', 1)->count();
        $tasaError = ($totalRetornados > 0) ? ($conObservaciones / $totalRetornados) * 100 : 0;

        // Gráficos de apoyo
        $distribucionObs = $this->getObservationDistribution($retornados);
        $flujoMensual = $this->getMonthlyFlow($records);

        // El topUsuarios se devuelve desde getFilteredMetrics.
        $topUsuariosPlaceholder = ['title' => 'Usuarios Solicitantes', 'data' => []];

        return [
            'totalAsignados' => $totalAsignados,
            'pendientesGesi' => $pendientesGesi,
            'pendientesIngresoGesi' => $pendientesIngresoGesi,
            'tiempoPromedioCiclo' => $tiempoPromedioCiclo,
            'tasaError' => $tasaError,
            'flujoMensual' => $flujoMensual,
            'distribucionObservaciones' => $distribucionObs,
            'topUsuarios' => $topUsuariosPlaceholder,
        ];
    }

    // --- FUNCIONES HELPER ---

    private function getObservationDistribution($records)
    {
        $tipos = ['Campo Vacío', 'Dato Inconsistente', 'Error de Formato'];
        $conteo = [0, 0, 0];
        foreach ($records->where('tiene_observacion', 1) as $record) {
            $obsText = strtolower($record->observaciones ?? '');
            if (str_contains($obsText, 'vacío')) {
                $conteo[0]++;
            } elseif (str_contains($obsText, 'inconsistente')) {
                $conteo[1]++;
            } elseif (str_contains($obsText, 'formato')) {
                $conteo[2]++;
            }
        }
        return ['tipos' => $tipos, 'conteo' => $conteo];
    }

    private function getMonthlyFlow($records)
    {
        $flow = $records->groupBy(function ($r) {
            return Carbon::parse($r->fecha_solicitud)->format('Y-m');
        })
            ->map(function ($group) {
                return [
                    'solicitados' => $group->count(),
                    'Gesi' => $group->whereNotNull('package_id')->count(),
                    'retornados' => $group->whereNotNull('fecha_retorno_entorno')->count(),
                    'month_label' => Carbon::parse($group->first()->fecha_solicitud)->isoFormat('MMM YY')
                ];
            })
            ->sortBy(fn($value, $key) => $key)
            ->values();

        return [
            'meses' => $flow->pluck('month_label')->toArray(),
            'solicitados' => $flow->pluck('solicitados')->toArray(),
            'Gesi' => $flow->pluck('Gesi')->toArray(),
            'retornados' => $flow->pluck('retornados')->toArray()
        ];
    }

    private function digitizersHome()
    {
        $role_id_technicians = [7, 10, 12];
        $technicians = User::whereIn('role_id', $role_id_technicians)->where('environment_id', 0)->where('is_active', 1)->get();
        $dataUser = DataUser::where('id_user', Auth::id())->first();

        // $User_tecnicos = User::where('role_id', 12)->where('is_active', 1)->where('environment_id', 0)->where('is_admin', 1)->get();

        $pendientesPorEntorno = $this->getPendientesPorDigitar();

        return view('dig.home', [
            'technicians' => $technicians,
            'userData' => $dataUser?->url_img,
            'pendientesPorEntorno' => $pendientesPorEntorno,
            'totalPendientesDigitar' => $pendientesPorEntorno->sum('total'),
            'totalVencidasDigitar' => $pendientesPorEntorno->sum('vencidas'),
            'totalActualizacionDigitar' => $pendientesPorEntorno->sum('actualizacion'),
            'totalNuevasDigitar' => $pendientesPorEntorno->sum('nuevas'),
        ]);
    }

    /**
     * Fichas recibidas por GESI (status 11) que aún no han sido digitadas (status 4),
     * agrupadas por entorno. El plazo para digitar una ficha es de 2 días desde que
     * GESI la recibió (registrado en reception_status_histories con new_status = 11).
     *
     * Cada pendiente se clasifica además en:
     * - "actualizacion": reingresos (mismo criterio que en el dashboard admin: fecha_intervencion
     *   de otro mes, o es el último id de un file_number + format_id con varios registros).
     * - "nuevas": registros únicos (sin duplicado) cuya fecha_intervencion sí es de este mes.
     */
    private function getPendientesPorDigitar(?string $inicioMes = null, ?string $finMes = null)
    {
        $inicioMes = $inicioMes ?: Carbon::now()->startOfMonth()->toDateString();
        $finMes    = $finMes    ?: Carbon::now()->endOfMonth()->toDateString();

        // Identifica el último ID de cada par (file_number, format_id) con duplicados,
        // usando un JOIN en vez de construir una lista IN(...) potencialmente enorme.
        $duplicateSub = DB::table('receptions')
            ->select('file_number', 'format_id', DB::raw('MAX(id) as last_id'))
            ->groupBy('file_number', 'format_id')
            ->havingRaw('COUNT(*) > 1');

        return DB::table('receptions')
            ->leftJoin('environments', 'environments.id', '=', 'receptions.environment')
            ->leftJoinSub($duplicateSub, 'dup', fn($j) =>
                $j->on('dup.file_number', '=', 'receptions.file_number')
                  ->on('dup.format_id', '=', 'receptions.format_id')
                  ->on('dup.last_id', '=', 'receptions.id')
            )
            ->select(
                'receptions.environment as environment_id',
                'environments.entorno as environment_name',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN DATEDIFF(NOW(), receptions.fecha_intervencion) >= 2 THEN 1 ELSE 0 END) as vencidas'),
                DB::raw('MIN(receptions.fecha_intervencion) as fecha_mas_antigua')
            )
            ->selectRaw(
                'SUM(CASE WHEN receptions.fecha_intervencion NOT BETWEEN ? AND ? OR dup.last_id IS NOT NULL THEN 1 ELSE 0 END) as actualizacion',
                [$inicioMes, $finMes]
            )
            ->selectRaw(
                'SUM(CASE WHEN receptions.fecha_intervencion BETWEEN ? AND ? AND dup.last_id IS NULL THEN 1 ELSE 0 END) as nuevas',
                [$inicioMes, $finMes]
            )
            ->where('receptions.status', 11)
            ->whereBetween('receptions.created_at', [$inicioMes, $finMes])
            ->groupBy('receptions.environment', 'environments.entorno')
            ->orderByDesc('total')
            ->get();
    }

    private function guestHome()
    {
        $user = Auth::user();

        $dataLicence = License::where('user_id', $user->id)->first();

        if (!$dataLicence) {
            return view('guest.index', [
                'licenceData' => null,
                'message' => 'No se encontraron datos para este usuario.'
            ]);
        }

        return view('guest.index', [
            'licenceData' => $dataLicence
        ]);
    }

    public function membersHome(Request $request)
    {
        $userAuth = Auth::user();
        $mesSeleccionado = $request->get('month', date('m'));
        $anioSeleccionado = $request->get('year', date('Y'));
        $fechaInicio = Carbon::create($anioSeleccionado, $mesSeleccionado, 1)->startOfMonth();
        $fechaFin = $fechaInicio->copy()->endOfMonth();

        // 1. Query Base: Lo que el usuario intervino este mes
        $baseQuery = Reception::whereBetween('fecha_intervencion', [$fechaInicio, $fechaFin])
            ->whereHas('users', function ($q) use ($userAuth) {
                $q->where('users.id', $userAuth->id);
            });

        $recepcionIds = (clone $baseQuery)->pluck('id');
        $misFileNumbers = (clone $baseQuery)->pluck('file_number')->unique();

        // 2. Lógica de Actualizaciones (CRUCIAL):
        // Buscamos clones vinculados a TUS números de ficha, 
        // pero cuya fecha de creación sea la del mes actual (independiente de la intervención).
        $actualizacionesQuery = Reception::whereIn('file_number', $misFileNumbers)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin]) // Filtramos por creación del clon
            ->whereDoesntHave('users', function ($q) use ($userAuth) {
                $q->where('users.id', $userAuth->id);
            });

        $actualizacionesIds = (clone $actualizacionesQuery)->pluck('id');
        $allRelatedIds = $recepcionIds->merge($actualizacionesIds);

        // 3. Diccionario y Formatos
        $formatosHijos = DB::table('adicional_format')->whereNotNull('id_format_adicional')->pluck('id_format_adicional');
        $statusNames = [
            10 => 'Rechazado',
            6 => 'Entregado profesional',
            1 => 'Pendiente aprobación',
            2 => 'Sellado',
            3 => 'En proceso de entrega a GESI',
            11 => 'Recibido GESI',
            12 => 'Entregado digitador',
            4 => 'En digitación',
            5 => 'En proceso de devolución a entorno',
            14 => 'Recibido entorno'
        ];

        // 4. Conteos de Estados (Universo Total)
        $conteosPorEstado = collect($statusNames)->mapWithKeys(fn($name) => [$name => 0]);
        $conteosReales = Reception::whereIn('id', $allRelatedIds)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')->get();

        foreach ($conteosReales as $item) {
            $nombreEstado = $statusNames[$item->status] ?? "Estado {$item->status}";
            $conteosPorEstado[$nombreEstado] = $item->total;
        }
        $conteosPorEstado = $conteosPorEstado->sortDesc();

        // 5. Productividad y Calidad — misma lógica que usa env.traceability:
        // solo se cuenta la observación más reciente por cada ficha/formato del usuario.
        $latestObservationsByFile = Productivity::latestObservationsForUser($userAuth->id, $fechaInicio, $fechaFin);

        $totalObservaciones = $latestObservationsByFile->count();
        $recepcionesConObs = $latestObservationsByFile->pluck('receptions.file_number')->unique()->count();
        $baseConMasObservaciones = $latestObservationsByFile
            ->groupBy(fn ($observation) => $observation->receptions->format_id)
            ->map(fn ($group) => (object) [
                'base' => $group->first()->receptions->bases->name ?? 'Formato',
                'total' => $group->count(),
            ])
            ->sortByDesc('total')
            ->first();

        // 5b. Consecutivos solicitados/asignados al usuario este mes
        $misConsecutivosQuery = Reception::whereNotNull('consecutivo')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->whereHas('users', function ($q) use ($userAuth) {
                $q->where('users.id', $userAuth->id);
            });

        $misConsecutivos = (clone $misConsecutivosQuery)->get(['id', 'file_number', 'consecutivo']);
        $totalConsecutivos = $misConsecutivos->count();
        $ultimoConsecutivo = $misConsecutivos->sortByDesc('consecutivo')->first();

        // Una ficha puede recibir más de un registro con consecutivo (ej. al añadir un
        // formato adicional): si ya existía un registro anterior con ese file_number,
        // es una actualización; si es la primera vez que aparece, es una ficha nueva.
        $primerRegistroPorFicha = Reception::whereIn('file_number', $misConsecutivos->pluck('file_number'))
            ->select('file_number', DB::raw('MIN(id) as primer_id'))
            ->groupBy('file_number')
            ->pluck('primer_id', 'file_number');

        $nuevasConsecutivos = $misConsecutivos->filter(
            fn ($r) => ($primerRegistroPorFicha[$r->file_number] ?? null) == $r->id
        )->count();
        $actualizacionesConsecutivos = $totalConsecutivos - $nuevasConsecutivos;

        // 6. Gráfica Histórica (Solo Recepciones del Usuario)
        $seisMesesAtras = Carbon::now()->subMonths(5)->startOfMonth();
        $tendenciaMensual = Reception::where('fecha_intervencion', '>=', $seisMesesAtras)->whereHas('users', fn($q) => $q->where('users.id', $userAuth->id))->select(DB::raw("DATE_FORMAT(fecha_intervencion, '%Y-%m') as mes_anio"), DB::raw("COUNT(*) as total_recepciones"))->groupBy('mes_anio')->orderBy('mes_anio', 'ASC')->get();

        $labels = [];
        $dataRecepciones = [];
        foreach ($tendenciaMensual as $dato) {
            $labels[] = Carbon::parse($dato->mes_anio . '-01')->translatedFormat('M Y');
            $dataRecepciones[] = $dato->total_recepciones;
        }

        // 7. Dashboard Array Completo
        $dashboard = [
            'total_fichas' => $misFileNumbers->count(),
            'total_recepciones' => (clone $baseQuery)->count(),
            'total_actualizaciones' => (clone $actualizacionesQuery)->count(),
            'actualizaciones_digitadas' => DB::table('productivity')->whereIn('file_id', $actualizacionesIds)->distinct()->count('file_id'),
            'digitadas' => DB::table('productivity')->whereIn('file_id', $allRelatedIds)->distinct()->count('file_id'),
            'pendientes_sellar' => (clone $baseQuery)->where('status', 1)->count(),
            'devueltas' => (clone $baseQuery)->whereNotNull('return_date')->count(),
            'por_estado' => $conteosPorEstado,
            'total_observaciones' => $totalObservaciones,
            'recepciones_con_obs' => $recepcionesConObs,
            'base_mas_observaciones' => $baseConMasObservaciones,
            'total_consecutivos' => $totalConsecutivos,
            'nuevas_consecutivos' => $nuevasConsecutivos,
            'actualizaciones_consecutivos' => $actualizacionesConsecutivos,
            'ultimo_consecutivo' => $ultimoConsecutivo,
            'grafica' => ['labels' => $labels, 'recepciones' => $dataRecepciones, 'errores' => []],
            'filtros' => ['mes' => $mesSeleccionado, 'anio' => $anioSeleccionado],
        ];

        return view('members.home', compact('dashboard'));
    }

    public function exportDigitadores(Request $request)
    {
        $fechaCorte = Carbon::create(2026, 2, 10)->startOfDay();

        /* =========================
    * 1️⃣ RANGO DE FECHAS
    * ========================= */
        $inicio = $request->input('init_date') ? Carbon::parse($request->input('init_date'))->startOfDay() : now()->subMonths(3)->startOfDay();
        $fin = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : now()->endOfDay();

        $productivity = collect();
        $tiempoCalculado = collect();

        if ($inicio < $fechaCorte) {
            $queryProductivity = Productivity::with(['user', 'dataUser:id,id_user,environment_assignment_id'])
                ->select('productivity.dig_id', DB::raw('SUM(formats.header_time) + SUM(productivity.quantity_users * formats.body_time) as total'))
                ->leftJoin('formats', 'productivity.base_id', '=', 'formats.id');

            if (!empty($request->environment)) {
                $queryProductivity->leftJoin('data_users', 'productivity.dig_id', '=', 'data_users.id_user')
                    ->where('data_users.environment_assignment_id', $request->environment);
            }

            $productivity = $queryProductivity
                ->whereBetween('productivity.created_at', [$inicio, min($fin, $fechaCorte->copy()->subSecond())])
                ->groupBy('productivity.dig_id')->get();
        }

        /* ==============================
    * 2️⃣ TIEMPO CALCULADO (SDS)
    * ============================== */
        if ($fin >= $fechaCorte) {
            $tiempoCalculado = Productivity_sds::whereBetween('created_at', [max($inicio, $fechaCorte), $fin])
                ->whereNotNull('dig_id')
                ->select('dig_id', DB::raw('SUM(calculation_time) as total'))
                ->groupBy('dig_id')
                ->pluck('total', 'dig_id');
        }

        /* ==============================
    * 3️⃣ UNIFICAR PRODUCTIVIDAD
    * ============================== */
        foreach ($productivity as $prod) {
            $extraSegundos = $tiempoCalculado->get($prod->dig_id) ?? $tiempoCalculado->get((int)$prod->dig_id) ?? 0;
            $prod->total = (float)$prod->total + ((float)$extraSegundos / 60);
        }

        foreach ($tiempoCalculado as $dig_id => $totalExtraSegundos) {
            if (!$productivity->firstWhere('dig_id', $dig_id)) {
                $productivity->push((object)[
                    'dig_id' => $dig_id,
                    'total'  => (float)$totalExtraSegundos / 60,
                    'user'   => User::find($dig_id),
                    'dataUser' => DataUser::where('id_user', $dig_id)->first()
                ]);
            }
        }

        /* ==============================
    * 4️⃣ OTROS TIEMPOS (AQUÍ EL CAMBIO)
    * ============================== */
        $queryOt = OtherTime::select('id_user', DB::raw('SUM(time) as total'))
            ->whereBetween('date_time', [$inicio, $fin])
            ->where('approved', 1);

        if (!empty($request->environment)) {
            $queryOt->whereHas('dataUser', function ($q) use ($request) {
                $q->where('environment_assignment_id', $request->environment);
            });
        }

        // Usamos all() para asegurar que el array sea plano y forzamos claves a string para evitar fallos de tipo
        $otMap = $queryOt->groupBy('id_user')->pluck('total', 'id_user')->toArray();



        /* ==============================
    * 5️⃣ CONSOLIDACIÓN TOTAL
    * ============================== */
        $allUserIds = $productivity->pluck('dig_id')->merge(array_keys($otMap))->unique();

        $dataExport = $allUserIds->map(function ($userId) use ($productivity, $otMap, $inicio, $fin) {
            $prodRow = $productivity->firstWhere('dig_id', $userId);

            $user = $prodRow->user ?? User::find($userId);
            $dataUser = $prodRow->dataUser ?? DataUser::where('id_user', $userId)->first();

            // --- LA SUMA CRÍTICA ---
            $minutosProd = (float) ($prodRow->total ?? 0);
            // Intentamos buscar el ID como lo devuelva el array (string o int)
            $horasOt = (float) ($otMap[$userId] ?? $otMap[(int)$userId] ?? 0);


            $minutosOt = $horasOt * 60;

            $totalGeneralMinutos = $minutosProd + $minutosOt;

            $totalGeneralHoras = round($totalGeneralMinutos / 60, 2);

            // almacenar la meta de horas si el valor es mayor cambairlo hasta detectar las horas maximas


            $metaHoras = 184;
            $metaMinutos = $metaHoras * 60;

            $nombreCompleto = $user ? trim($user->name . ' ' . $user->last_name) : null;

            return [
                'digitador'   => $nombreCompleto ?: 'Usuario ID: ' . $userId,
                'entorno'     => $this->getNombreEntorno($dataUser->environment_assignment_id ?? null),
                'total_horas' => $totalGeneralHoras,
                'meta'        => $metaHoras,
                'porcentaje'  => $metaMinutos > 0 ? round(($totalGeneralMinutos / $metaMinutos) * 100, 1) . '%' : '0%',
                'periodo'     => $inicio->format('d/m/Y') . ' - ' . $fin->format('d/m/Y'),
            ];
        });

        return Excel::download(
            new DigitadoresExport($dataExport, $inicio->toDateString(), $fin->toDateString()),
            'reporte_productividad.xlsx'
        );
    }

    /* =========================
     * FUNCIÓN AUXILIAR
     * ========================= */
    private function getNombreEntorno($id)
    {
        $nombres = [
            2 => 'Laboral',
            3 => 'Educativo',
            4 => 'Comunitario',
            6 => 'Institucional'
        ];

        return $nombres[$id] ?? 'Otro';
    }
}
