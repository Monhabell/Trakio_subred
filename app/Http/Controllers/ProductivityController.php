<?php

namespace App\Http\Controllers;

// Modelos
use App\Models\Format;
use App\Models\OtherTime;
use App\Models\Package;
use App\Models\Productivity;
use App\Models\Productivity_sds;
use App\Models\Reception;
use App\Models\DataUser;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

// Requests
use App\Http\Requests\UpdateProductivityRequest;
use App\Http\Requests\StoreProductivityRequest;
use App\Models\User;
// Librerias
use Carbon\Carbon;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class ProductivityController extends Controller
{
    public function index(): View
    {
        return view('dig.productivity');
    }

    public function currentMonthProductivity($startDate, $endDate)
    {
        try {
            $userid = Auth::id();
            $day = [];

            // Formatear las fechas recibidas
            $startDateFormatted = Carbon::parse($startDate)->startOfDay();
            $endDateFormatted = Carbon::parse($endDate)->endOfDay();

            // Obtener los resultados entre las fechas y tiempos proporcionados
            $user_files = Productivity::whereBetween('created_at', [$startDateFormatted, $endDateFormatted])->where('dig_id', $userid)->get();
            $dayProductivity = Productivity::whereDate('created_at', Carbon::today())->where('dig_id', $userid)->get();

            // Pre-cargar todos los formats en una sola consulta para evitar N+1
            $allBaseIds = $user_files->pluck('base_id')->merge($dayProductivity->pluck('base_id'))->unique();
            $formatsMap = Format::whereIn('id', $allBaseIds)->get()->keyBy('id');

            $totalSum = 0;

            foreach ($user_files as $file) {
                $cant_us = $file->quantity_users;
                $format = $formatsMap->get($file->base_id);
                if ($format) {
                    $header_time = $format->header_time;
                    $body_time = $format->body_time;
                    $time_us_Cant = $body_time * $cant_us;
                    $total_time = $header_time + $time_us_Cant;
                    $totalSum += $total_time;
                }
            }

            $otherTime = OtherTime::where('id_user', $userid)->whereBetween('date_time', [$startDateFormatted, $endDateFormatted])->where('approved', 1)->get();
            $otherTimeResult = 0;

            foreach ($otherTime as $time) {
                $resultOther = $time->time;
                $otherTimeResult += $resultOther;
            };

            $averageTime = ($totalSum / 60) + $otherTimeResult;

            $cantidadDigMes = $user_files->count();
            $totalSumDay = 0;

            foreach ($dayProductivity as $dayProduc) {
                $format = $formatsMap->get($dayProduc->base_id);
                $cant_us = $dayProduc->quantity_users;
                if ($format) {
                    $header_time = $format->header_time;
                    $body_time = $format->body_time;
                    $time_us_Cant = $body_time * $cant_us;
                    $total_time = $header_time + $time_us_Cant;

                    $totalSumDay += $total_time;
                }
            }

            $averageTimeDay = $totalSumDay / 60;
            $cantidadDigDay = $dayProductivity->count();
            $day[] = [
                'horas' => $averageTimeDay,
                'Cant' => $cantidadDigDay,
            ];

            $requestsOtMonth = OtherTime::where('id_user', $userid)->whereBetween('date_time', [$startDateFormatted, $endDateFormatted])->get();
            // Agrupar resultados por día
            $groupedResults = $user_files->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

            // Array para almacenar los tiempos por día
            $dataPorDia = [];

            foreach ($groupedResults as $days => $dailyResults) {
                $dailySum = 0;

                foreach ($dailyResults as $result) {
                    $cant_us = $result->quantity_users;
                    $format = $formatsMap->get($result->base_id);
                    if ($format) {
                        $header_time = $format->header_time;
                        $body_time = $format->body_time;
                        $time_us_Cant = $body_time * $cant_us;
                        $total_time = $header_time + $time_us_Cant;

                        $dailySum += $total_time;
                    }
                }

                // Guardar el total de tiempo por día en minutos y convertir a horas
                $dataPorDia[] = [
                    'day' => $days,
                    'totalTime' => $dailySum / 60, // Convertir a horas
                    'cantidad' => $dailyResults->count(),
                ];
            }

            $entorno = DataUser::where('id_user', $userid)->first();

            return response()->json([
                'totalSum' => $totalSum,
                'averageTime' => $averageTime,
                'cantidadDigMes' => $cantidadDigMes,
                'otherTimes' => $requestsOtMonth,
                'day' => $day,
                'data_mes' => $dataPorDia,
                'entorno' => $entorno->environment_assignment_id,

            ]);
        } catch (Exception $e) {
            Log::error('Error al obtener la productividad del mes: ' . $e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud'], 500);
        }
    }

    public function productivityMonth()
{
    $userId = Auth::id();

    // 1. Rango: Mes actual y Fechas de Control
    $init_date = Carbon::now()->startOfMonth();
    $end_date = Carbon::now()->endOfMonth();
    $today = Carbon::now()->startOfDay();

    // 2. CÁLCULO DE PRODUCTIVIDAD GLOBAL (Para Rankings y Promedios)

    // Sumatoria de Segundos de Digitación (Tabla Productivity_sds) en el mes actual
    $allSegundosDigitacion = Productivity_sds::whereBetween('created_at', [$init_date, $end_date])
        ->select('dig_id', DB::raw('SUM(calculation_time) as total'))
        ->groupBy('dig_id')
        ->pluck('total', 'dig_id');

    // Otros Tiempos Aprobados (Trae horas decimales directamente de la base de datos)
    $allOtrosTiempos = OtherTime::where('approved', 1)
        ->whereBetween('date_time', [$init_date, $end_date])
        ->select('id_user', DB::raw('SUM(time) as total'))
        ->groupBy('id_user')
        ->pluck('total', 'id_user');

    // 3. CONSTRUIR RANKING DE USUARIOS
    $allUserIds = $allSegundosDigitacion->keys()
        ->concat($allOtrosTiempos->keys())
        ->unique();

    $rankings = $allUserIds->map(function ($id) use ($allSegundosDigitacion, $allOtrosTiempos) {        
        // Conversión: Segundos a horas decimales (Segundos / 3600)
        $horasDigitacion = ($allSegundosDigitacion->get($id, 0) / 60) / 60;

        // Otros tiempos YA VIENE EN HORAS (No se vuelve a dividir)
        $horasOtros = $allOtrosTiempos->get($id, 0);

        // Sumamos todo unificado en la misma unidad: HORAS
        $totalHorasSumadas = $horasDigitacion + $horasOtros;

        return [
            'user_id' => (int) $id,
            'h_digitacion' => $horasDigitacion, 
            'h_otros' => $horasOtros,
            'total_horas' => $totalHorasSumadas
        ];
    })->sortByDesc('total_horas')->values();

    // 4. ESTADÍSTICAS ESPECÍFICAS DEL USUARIO LOGUEADO
    $myStats = $rankings->firstWhere('user_id', $userId) ?: [
        'total_horas' => 0,
        'h_digitacion' => 0,
        'h_otros' => 0
    ];

    $myTotal = $myStats['total_horas'];
    $horasDigitacion = $myStats['h_digitacion'];
    $horasOtrosTiempos = $myStats['h_otros'];

    // Posición en el ranking
    $myIndex = $rankings->search(fn($item) => $item['user_id'] == $userId);

    // Usuario superior para el mensaje de "Te falta X para superar a..."
    $superiorUser = ($myIndex !== false && $myIndex > 0) ? $rankings[$myIndex - 1] : null;

    // Promedio y Máximos del equipo
    $promedioGlobal = $rankings->count() > 0 ? $rankings->avg('total_horas') : 0;
    $maxHorasEquipo = $rankings->count() > 0 ? $rankings->max('total_horas') : 0;

    // Diferencias vs promedio y siguiente usuario
    $diffPromedio = $myTotal - $promedioGlobal;
    $faltaSiguiente = $superiorUser ? $superiorUser['total_horas'] - $myTotal : 0;

    // 5. DATOS ADICIONALES (Fichas y Gráfica)
    // Nota: Se asume que el conteo de fichas por volumen ahora también se saca de Productivity_sds
    $fichasHoy = Productivity_sds::where('dig_id', $userId)->whereDate('created_at', $today)->count();
    $fichasMes = Productivity_sds::where('dig_id', $userId)->whereBetween('created_at', [$init_date, $end_date])->count();

    // Progreso del día actual (Digitación + Otros Tiempos aprobados de hoy)
    $segundosDigitacionHoy = Productivity_sds::where('dig_id', $userId)->whereDate('created_at', $today)->sum('calculation_time');
    $horasDigitacionHoy = ($segundosDigitacionHoy / 60) / 60;
    $horasOtrosHoy = OtherTime::where('id_user', $userId)->where('approved', 1)->whereDate('date_time', $today)->sum('time');
    $horasHoy = $horasDigitacionHoy + $horasOtrosHoy;
    $metaDiaria = 8;
    $porcentajeMetaDia = min(round(($horasHoy / $metaDiaria) * 100), 100);

    // Datos para la gráfica de los últimos 7 días
    $chartData = Productivity_sds::where('dig_id', $userId)
        ->where('created_at', '>=', Carbon::now()->subDays(6))
        ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
        ->groupBy('date')
        ->orderBy('date', 'ASC')
        ->get();

    // Listado de últimas 5 actividades extra
    $actividadesExtra = OtherTime::where('id_user', $userId)
        ->orderBy('date_time', 'desc')
        ->take(5)
        ->get()
        ->map(function ($item) {
            return [
                'descripcion' => $item->description,
                'fecha' => Carbon::parse($item->date_time)->format('d M Y'),
                'horas' => $this->formatHoursToClock($item->time),
                'status' => $item->approved == 1 ? 'Aprobado' : 'Pendiente'
            ];
        });

    // 6. RESPUESTA JSON FINAL
    return response()->json([
        'status' => 'success',
        'metrics' => [
            'total_horas' => $this->formatHoursToClock($myTotal),
            'total_horas_decimal' => round($myTotal, 4),

            'fichas_hoy' => $fichasHoy,
            'fichas_mes' => $fichasMes,
            'porcentaje_meta' => min(round(($myTotal / 184) * 100), 100), // Meta estándar 184h
            'horas_digitacion' => $this->formatHoursToClock($horasDigitacion),
            'horas_otros' => $this->formatHoursToClock($horasOtrosTiempos),

            // Progreso del día actual (Meta diaria fija de 8h)
            'horas_hoy' => $this->formatHoursToClock($horasHoy),
            'horas_hoy_con_segundos' => $this->formatHoursToClockWithSeconds($horasHoy),
            'horas_hoy_decimal' => round($horasHoy, 2),
            'porcentaje_meta_dia' => $porcentajeMetaDia,
            'horas_hasta_ayer' => $this->formatHoursToClock($myTotal - $horasHoy),

            // Métricas comparativas (Benchmarking) Formateadas a Reloj
            'promedio_equipo' => $this->formatHoursToClock($promedioGlobal),
            'atraso_vs_promedio' => ($diffPromedio < 0 ? '-' : '') . $this->formatHoursToClock(abs($diffPromedio)), 
            'maximo_alcanzado' => $this->formatHoursToClock($maxHorasEquipo),
            'falta_para_superar_siguiente' => $this->formatHoursToClock($faltaSiguiente),
            
            'posicion_ranking' => $myIndex !== false ? $myIndex + 1 : 'N/A',
            'total_colaboradores' => $rankings->count()
        ],
        'chart' => [
            'labels' => $chartData->pluck('date'),
            'values' => $chartData->pluck('count')
        ],
        'actividades' => $actividadesExtra
    ]);
}

/**
 * Helper para convertir horas decimales (ej: 71.75) a formato reloj (ej: "71:45")
 */
private function formatHoursToClock($decimalHours)
{
    if (!$decimalHours || $decimalHours <= 0) {
        return "00:00";
    }

    $hours = floor($decimalHours);
    // Extrae los decimales y los multiplica por 60 para sacar los minutos reales
    $minutes = round(($decimalHours - $hours) * 60);

    // Por si el redondeo da 60 minutos exactos
    if ($minutes == 60) {
        $hours++;
        $minutes = 0;
    }

    return sprintf('%02d:%02d', $hours, $minutes);
}

/**
 * Igual que formatHoursToClock pero incluye los segundos (HH:MM:SS).
 */
private function formatHoursToClockWithSeconds($decimalHours)
{
    if (!$decimalHours || $decimalHours <= 0) {
        return "00:00:00";
    }

    $totalSeconds = round($decimalHours * 3600);
    $hours = floor($totalSeconds / 3600);
    $minutes = floor(($totalSeconds % 3600) / 60);
    $seconds = $totalSeconds % 60;

    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}

    public function store(StoreProductivityRequest $request)
    {

        $reception = Reception::with([
            'bases' => function ($query) {
                // Tabla pivot formats_productivity_sds
                $query->with('format_sds');
            }
        ])->where('id', $request->file_id)->first();


        $formatId = $reception->format_id;
        $formatsIdNoSds = [56, 57, 58, 59];

        $idsSdsDisponibles = $reception->bases->format_sds ? $reception->bases->format_sds->pluck('id_sds')->toArray() : [];

        // Consultar también que la fecha no corresponda al día actual al último registro 
        $search_sds = Productivity_sds::whereIn('sds_id', $idsSdsDisponibles)
            ->where('file_number', $reception->file_number)
            ->where(function ($query) {
                $query->whereDate('created_at', Carbon::today())
                    ->orWhereDate('updated_at', Carbon::today());
            })
            ->first();

        // se comenta por que no entra a funcionar trakio injector  

        // if (!in_array($formatId, $formatsIdNoSds)) { // Si la ficha a reportar no tiene ID SDS
        //     if (!$search_sds) { // Si no encuentra ficha en productivity_sds
        //         return response()->json(['error' => 'La ficha parece no estar digitada en GESI Form'], 500);
        //     }
        // }

        DB::beginTransaction();
        try {
            // Obtener datos del request
            $data = $request->only([
                'file_id',
                'base_id',
                'observations',
                'quantity_users',
                'type'
            ]);

            $digId = Auth::id();
            $ficha = $data['file_id'];

            $existing = Productivity::where('file_id', $ficha)->first();
            $savedProductivity = null;

            if ($existing) {
                return response()->json(['error' => 'Ficha ya reportada'], Response::HTTP_NOT_FOUND);
            } else {
                // Crear uno nuevo
                $savedProductivity = Productivity::create($data + ['dig_id' => $digId]);
                if ($savedProductivity) {
                    $search_sds->update([
                        'dig_id' => Auth::id(),
                        'updated_at' => now(),
                    ]);

                    $reception->update(['status' => 4]); // Actualizar estado a "Digitado"
                    $this->updateStatusPackage($request->package_id);
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Datos guardados correctamente',
                'calculation_time' => $search_sds?->calculation_time ?? 0,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Error al guardar la productividad'], 500);
        }
    }

    private function updateStatusPackage($package_id)
    {
        $package_files = Reception::where('package_id', $package_id)
            ->leftJoin('productivity', 'productivity.file_id', '=', 'receptions.id')
            ->select(
                'receptions.package_id',
                DB::raw('COUNT(receptions.id) AS count'),
                DB::raw('COUNT(productivity.id) AS productivity_count')
            )
            ->groupBy('receptions.package_id')
            ->first();

        if (!$package_files){
            return false;
        }

        $receptions_count = $package_files->count;
        $productivity_count = $package_files->productivity_count;

        $packageStatus = $productivity_count < $receptions_count ? 2 : 3;

        Package::where('id', $package_id)->update([
            'status' => $packageStatus,
            'end_at' => $packageStatus == 3 ? now() : null
        ]);

        return true;
    }

    public function edit(Productivity $productivity)
    {
        $productivity = $productivity->load([
            'user:id,name,last_name',
            'base:id,name',
            'receptions:id,file_number'
        ]);
        return view('adm.traceability.edit', [
            'productivity' => $productivity
        ]);
    }

    public function update(UpdateProductivityRequest $request, Productivity $productivity)
    {
        DB::beginTransaction();
        try {
            $data = [
                'observations' => $request->observations
            ];

            if ($request->has('type')) {
                $data['type'] = $request->type;
            }

            if ($request->has('quantity_users')) {
                $data['quantity_users'] = $request->quantity_users;
            }

            $productivity->update($data);

            DB::commit();

            $successMessage = 'Observaciones actualizadas correctamente';

            if ($request->wantsJson()) {
                return response()->json(['message' => $successMessage], Response::HTTP_OK);
            }

            return redirect()->back()->with(['success' => $successMessage]);
        } catch (\Exception $e) {
            DB::rollBack();


            $errorMessage = 'Error al actualizar la productividad';

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $errorMessage,
                    'error' => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return redirect()->back()->with(['error' => $errorMessage]);
        }
    }

    public function delete(Productivity $productivity)
    {
        DB::beginTransaction();
        try {
            $productivity->delete();

            DB::commit();
            return redirect()->route('adm.traceability')->with(['success' => 'Se ha eliminado la ficha de productividad']);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('adm.traceability')->with(['error' => 'Error al eliminar la productividad']);
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();
        try {
            $productivity = Productivity::withTrashed()->findOrFail($id);
            $productivity->restore();

            DB::commit();
            return redirect()->route('adm.traceability')->with(['success' => 'Se ha restaurado la ficha de productividad']);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('adm.traceability')->with(['error' => 'Error al restaurar la productividad']);
        }
    }

    public function productivityDay(Request $request)
    {
        try {
            $userid = Auth::id();

            // Obtener la fecha de hoy
            $today = Carbon::today();

            // Obtener los registros de productividad del día actual
            $dayProductivity = Productivity::whereDate('created_at', $today)
                ->where('dig_id', $userid)
                ->get();

            // Pre-cargar formats en una sola consulta
            $dayFormatsMap = Format::whereIn('id', $dayProductivity->pluck('base_id')->unique())->get()->keyBy('id');

            $totalSumDay = 0;

            foreach ($dayProductivity as $dayProduc) {
                $cant_us = $dayProduc->quantity_users;
                $time = $dayFormatsMap->get($dayProduc->base_id);

                if ($time) {
                    $time_ficha = $time->header_time;
                    $time_us = $time->body_time;
                    $totalSumDay += $time_ficha + ($time_us * $cant_us);
                }
            }

            $averageTimeDay = $totalSumDay / 60; // Convertir a horas
            $cantidadDigDay = $dayProductivity->count();

            $day = [
                'horas' => $averageTimeDay,
                'Cant' => $cantidadDigDay,
            ];
            $entorno = DataUser::where('id_user', $userid)->first();

            return response()->json(['day' => $day, 'entorno' => $entorno->environment_assignment_id]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud'], 500);
        }
    }

    /**
     * Data para dashboard administradores
     */
    public function dashboardAdmin(Request $request)
    {
        $init_date = Carbon::parse($request->range['init_date'])->startOfDay();
        $end_date = Carbon::parse($request->range['end_date'])->endOfDay();

        $fechaCorte = Carbon::create(2026, 2, 10)->startOfDay();

        $productivity = collect();
        $tiempoCalculado = collect();

        /**
         * ==============================
         * 1️⃣ PRODUCTIVIDAD ANTES DEL 10 FEB
         * ==============================
         */
        if ($init_date < $fechaCorte) {

            $queryProductivity = Productivity::with([
                'user',
                'dataUser:id,id_user,environment_assignment_id,url_img'
            ])
                ->select(
                    'productivity.dig_id',
                    DB::raw('SUM(formats.header_time) + SUM(productivity.quantity_users * formats.body_time) as total')
                )
                ->leftJoin('formats', 'productivity.base_id', '=', 'formats.id');

            if (!empty($request->environment)) {
                $queryProductivity->leftJoin('data_users', 'productivity.dig_id', '=', 'data_users.id_user')
                    ->where('data_users.environment_assignment_id', $request->environment);
            }

            $productivity = $queryProductivity
                ->whereBetween('productivity.created_at', [
                    $init_date,
                    min($end_date, $fechaCorte->copy()->subSecond())
                ])
                ->groupBy('productivity.dig_id')
                ->get();
        }

        /**
         * ==============================
         * 2️⃣ TIEMPO CALCULADO DESPUÉS DEL 10 FEB
         * ==============================
         */
        if ($end_date >= $fechaCorte) {
            $tiempoCalculado = Productivity_sds::whereBetween('created_at', [
                max($init_date, $fechaCorte),
                $end_date
            ])
                ->whereNotNull('dig_id') // <--- Esta línea filtra los nulos
                ->select('dig_id', DB::raw('SUM(calculation_time) as total'))
                ->groupBy('dig_id')
                ->pluck('total', 'dig_id');
        }

        /**
         * ==============================
         * 3️⃣ SUMAR TIEMPO CALCULADO
         * ==============================
         */
        foreach ($productivity as $prod) {

            $extra = $tiempoCalculado->get($prod->dig_id, 0);



            // convertir segundos a minutos
            $prod->total = ((float) $prod->total) + (((float) $extra) / 60);
        }

        /**
         * Usuarios que solo tienen registros después del 10
         */
        foreach ($tiempoCalculado as $dig_id => $totalExtra) {

            if (!$productivity->firstWhere('dig_id', $dig_id)) {

                $productivity->push((object) [
                    'dig_id' => $dig_id,
                    'total' => $totalExtra / 60,
                    'user' => User::find($dig_id),
                    'data_user' => DataUser::where('id_user', $dig_id)->first()
                ]);
            }
        }

        /**
         * ==============================
         * OTROS TIEMPOS
         * ==============================
         */
        $queryOt = OtherTime::with([
            'userId',
            'dataUser:id,id_user,environment_assignment_id'
        ])
            ->select('other_times.id_user', DB::raw('SUM(other_times.time) as total'))
            ->whereBetween('other_times.date_time', [$init_date, $end_date]);

        if (!empty($request->environment)) {
            $queryOt->leftJoin('data_users', 'other_times.id_user', '=', 'data_users.id_user')
                ->where('data_users.environment_assignment_id', $request->environment);
        }

        $ot = $queryOt->where('other_times.approved', 1)
            ->groupBy('other_times.id_user')
            ->get();


        return response()->json([
            'success' => true,
            'productivity' => $productivity,
            'other_times' => $ot
        ]);
    }


    public function observationsPercentage(Request $request)
    {
        try {
            $user = Auth::user();
            $noObservationsCount = Reception::where('environment', $user->environment_id)
                ->whereBetween('receptions.fecha_intervencion', [$request->input('init_date'), $request->input('end_date')])
                ->whereNull('productivity.observations')
                ->leftJoin('productivity', 'productivity.file_id', '=', 'receptions.id')
                ->count();

            $observations = Reception::with('bases:id,name')->where('environment', $user->environment_id)
                ->whereBetween('receptions.fecha_intervencion', [$request->input('init_date'), $request->input('end_date')])
                ->whereNotNull('productivity.observations')
                ->leftJoin('productivity', 'productivity.file_id', '=', 'receptions.id');

            $observationsCount = $observations->count();
            $percentageObservations = $observationsCount > 0 ? ($observationsCount / ($observationsCount + $noObservationsCount)) * 100 : 0;

            $environmentObservations = $observations->limit(30)->orderBy('productivity.created_at', 'desc')->get();

            $observationsByFormat = Reception::where('receptions.environment', $user->environment_id)
                ->whereBetween('receptions.fecha_intervencion', [$request->input('init_date'), $request->input('end_date')])
                ->whereNotNull('productivity.observations')
                ->leftJoin('productivity', 'productivity.file_id', '=', 'receptions.id')
                ->join('formats', 'receptions.format_id', '=', 'formats.id')
                ->select('formats.name', DB::raw('count(*) as total'))
                ->groupBy('formats.name')
                ->get();

            $observationsByFormat->pluck('total', 'name');

            if ($observationsCount) {
                return response()->json([
                    'data' => [
                        'percentageObservations' => $percentageObservations,
                        'observations' => $environmentObservations,
                        'observationsCountByFormat' => $observationsByFormat
                    ]
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'data' => [
                        'percentageObservations' => 0,
                        'observations' => [],
                        'observationsCountByFormat' => [],
                        'message' => 'No hay observaciones registradas'
                    ]
                ], Response::HTTP_OK);
            }
        } catch (\Throwable $th) {
            Log::error('Error al obtener el porcentaje de observaciones: ' . $th->getMessage());
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
