<?php

namespace App\Http\Controllers;

use App\Models\Format;
use App\Models\Reception;
use App\Models\ReceptionStatusHistory;
use App\Models\User;
use App\Models\ReceptionSisco;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReviewConsecutiveController extends Controller
{
    public function __construct()
    {
        \Carbon\Carbon::setLocale('es');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 1000);
            $today = now()->startOfDay();
            session(['previous_url' => url()->previous()]);

            $professionals = User::whereNotIn('role_id', [13, 12, 11, 7, 1])
                ->where('is_active', 1)
                ->where('environment_id', '!=', 0)
                ->get();

            $localidades = DB::table('localidades')->get();

            $filesQuery = Reception::with([
                'environment_file:id,entorno',
                'users' => function ($query) {
                    $query->select('users.id', 'users.name', 'users.last_name')->distinct();
                },
                'localidad:id,name',
                'bases:id,name',
                'interventionType:id,name',
            ]);
            // ->where('required_date', '>=', '2025-10-01 00:00:00');

            // --- Filtros dinámicos ---
            if ($request->filled('profesional')) {
                $filesQuery->whereHas('users', function ($query) use ($request) {
                    $query->where('user_id', $request->input('profesional'));
                });
            }

            if ($request->filled('environment')) {
                $filesQuery->where('environment', $request->input('environment'));
            }

            if ($request->filled('status')) {
                if ($request->input('status') == 3) {
                    $filesQuery->where(function ($query) {
                        $query->where('status', 4)->orWhere('status', 3);
                    });
                } else {
                    $filesQuery->where('status', $request->input('status'));
                }
            }

            // Si no se envió status, por defecto mostrar solo status = 1
            // if (!$request->filled('status')) {
            //     $filesQuery->where('status', 1);
            // }

            if ($request->filled('ficha')) {
                $filesQuery->where('file_number', 'like', '%' . $request->input('ficha') . '%');
            }

            if ($request->filled('localidad') && is_array($request->input('localidad'))) {
                $filesQuery->whereIn('localidad_id', $request->input('localidad'));
            }

            if ($request->filled('month_intervention')) {
                $filesQuery->whereMonth('fecha_intervencion', $request->input('month_intervention') ?? now()->month);
            }

            if ($request->filled('year_intervention')) {
                $filesQuery->whereYear('fecha_intervencion', $request->input('year_intervention'));
            }

            $delayAlerts = $this->getDelayAlerts($request);

            $alertsByUser = $delayAlerts->flatMap(function ($alert) {
                return $alert->users->map(function ($user) {
                    return [
                        'name' => trim(($user->name ?? '') . ' ' . ($user->last_name ?? '')),
                    ];
                });
            })
                ->filter(fn($item) => !empty($item['name']))
                ->groupBy('name')
                ->map(function ($items, $name) {
                    return [
                        'name' => $name,
                        'count' => $items->count(),
                    ];
                })
                ->sortByDesc('count')
                ->values();

            $files = $filesQuery
                ->orderBy('created_at', 'desc')
                ->orderBy('consecutivo', 'asc')
                ->orderBy('id', 'asc')
                ->paginate($perPage)
                ->appends(['per_page' => $perPage]);

            $consecutivos = ReceptionSisco::with([
                'users:id,name,last_name',
                'localidad:id,name',
                'environment:id,entorno',
                'format:id,name',
                'subnet:id,name',
                'statusHistories',
            ])
                ->whereHas('statusHistories', function ($query) {
                    $query->where('changed_by', Auth::id())
                        ->where('new_status', 2);
                })
                ->orderBy('id', 'desc')
                ->paginate(50)->appends(request()->query());

            return view('env.review-consecutives', [
                'files' => $files,
                'professionals' => $professionals,
                'localidades' => $localidades,
                'consecutivos' => $consecutivos,
                'delayAlerts' => $delayAlerts,
                'alertsByUser' => $alertsByUser,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading review consecutives: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar las fichas.');
        }
    }

    /**
     * Fichas con más de 5 días de retraso para los profesionales que el usuario autenticado ha entregado.
     */
    protected function getDelayAlerts(Request $request)
    {
        $today = now()->startOfDay();

        // Si el usuario eligió mes/año explícitamente, usar ese.
        // Si no, hasta el 4° día hábil del mes actual se muestran las alertas del mes anterior
        // (porque ese día se entregan las bases del mes anterior).
        if (!$request->filled('month_intervention') && !$request->filled('year_intervention')) {
            $fourthBusinessDay = $this->getFourthBusinessDay($today->year, $today->month);
            $referenceDate = $today->lte($fourthBusinessDay)
                ? $today->copy()->subMonth()
                : $today;
            $alertMonth = $referenceDate->month;
            $alertYear  = $referenceDate->year;
        } else {
            $alertMonth = $request->filled('month_intervention') ? (int) $request->input('month_intervention') : $today->month;
            $alertYear  = $request->filled('year_intervention')  ? (int) $request->input('year_intervention')  : $today->year;
        }

        $approvedProfessionalIds = DB::table('reception_user')
            ->join('receptions', 'receptions.id', '=', 'reception_user.reception_id')
            ->where('receptions.delivered_by', Auth::id())
            ->pluck('reception_user.user_id')
            ->unique()
            ->values();

        if ($approvedProfessionalIds->isEmpty()) {
            return collect();
        }

        return Reception::with([
            'environment_file:id,entorno',
            'bases:id,name',
            'users' => function ($query) {
                $query->select('users.id', 'users.name', 'users.last_name')->distinct();
            }
        ])
            ->select(['id', 'file_number', 'fecha_intervencion', 'status', 'environment', 'format_id', 'delivered_by'])
            ->whereIn('status', [1, 2])
            ->whereNotNull('fecha_intervencion')
            ->whereYear('fecha_intervencion', $alertYear)
            ->whereMonth('fecha_intervencion', $alertMonth)
            ->whereHas('users', function ($query) use ($approvedProfessionalIds) {
                $query->whereIn('users.id', $approvedProfessionalIds);
            })
            ->get()
            ->filter(function ($file) use ($today) {
                return !empty($file->fecha_intervencion)
                    && Carbon::parse($file->fecha_intervencion)
                        ->diffInDays($today) > 5;
            })
            ->map(function ($file) use ($today) {
                $file->days_delay = Carbon::parse($file->fecha_intervencion)
                    ->diffInDays($today);

                return $file;
            })
            ->sortByDesc('days_delay')
            ->values();
    }

    /**
     * Descarga en Excel (CSV) las fichas con retraso mostradas en el componente de alertas.
     */
    public function exportDelayAlerts(Request $request)
    {
        $delayAlerts = $this->getDelayAlerts($request);

        $header_titles = [
            'Número de ficha',
            'Entorno',
            'Formato',
            'Profesional(es)',
            'Días de retraso',
            'Fecha intervención',
            'Estado',
        ];

        return new StreamedResponse(function () use ($delayAlerts, $header_titles) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, $header_titles, ';');

            foreach ($delayAlerts as $alert) {
                fputcsv($handle, [
                    $alert->file_number,
                    $alert->environment_file->entorno ?? '',
                    $alert->bases->name ?? '',
                    $alert->users->map(fn($user) => trim($user->name . ' ' . $user->last_name))->implode(', '),
                    $alert->days_delay,
                    $alert->fecha_intervencion ? Carbon::parse($alert->fecha_intervencion)->format('d/m/Y') : '',
                    getStatusNameFile($alert->status),
                ], ';');
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="alertas_retraso_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    public function storeSisco(Request $request)
    {


        $user = Auth::user();

        try {
            return DB::transaction(function () use ($request, $user) {

                $quantity       = (int) $request->input('consecutive_quantity');
                $environmentId  = $request->input('consecutive_environment');
                $profesionalId  = $request->input('consecutive_profesional');

                $now = now();

                // 🔒 Bloqueo para evitar duplicados en concurrencia
                $lastReception = ReceptionSisco::where('environment_id', $environmentId)
                    ->lockForUpdate()
                    ->max('consecutivo');

                $currentNumber = $lastReception ? (int) $lastReception : 0;

                $receptions = [];

                // 🔹 Generar data en memoria
                for ($i = 1; $i <= $quantity; $i++) {

                    $number = $currentNumber + $i;

                    $localidad = str_pad($request->input('consecutive_localidad'), 2, '0', STR_PAD_LEFT);

                    $number_concatenado =  $user->subnet_id . '0' . $environmentId . $localidad . str_pad($number, 5, '0', STR_PAD_LEFT);

                    $receptions[] = [
                        'localidad_id'      => $request->input('consecutive_localidad'),
                        'environment_id'    => $environmentId,
                        'subnet_id'         => $user->subnet_id,
                        'format_id'         => $request->input('consecutive_format'),
                        'package_id'        => null,
                        'delivered_by'      => $user->id,
                        'consecutivo'       => str_pad($number, 5, '0', STR_PAD_LEFT),
                        'consecutivo_sisco' => $number_concatenado,
                        'batch_number'      => null,
                        'intervention_date' => $request->input('consecutive_fecha'),
                        'status'            => 2,
                        'created_at'        => $now,
                        'updated_at'        => $now,
                    ];
                }

                // 🚀 Insert masivo
                ReceptionSisco::insert($receptions);

                // 🔹 Obtener consecutivos generados
                $consecutivos = array_column($receptions, 'consecutivo');

                // 🔹 Traer registros reales con ID (IMPORTANTE)
                $insertedReceptions = ReceptionSisco::whereIn('consecutivo', $consecutivos)
                    ->orderBy('id')
                    ->get();



                // =========================
                // 🟢 HISTORIAL
                // =========================
                $history = [];

                foreach ($insertedReceptions as $rec) {
                    $history[] = [
                        'reception_sisco_id' => $rec->id,
                        'previous_status'    => $rec->status,
                        'new_status'         => $rec->status,
                        'changed_by'         => $user->id,
                        'created_at'         => $now,
                        'updated_at'         => $now,
                    ];
                }

                ReceptionStatusHistory::insert($history);

                // =========================
                // 🟢 TABLA PIVOTE
                // =========================
                $pivotData = [];

                foreach ($insertedReceptions as $rec) {
                    $pivotData[] = [
                        'reception_sisco_id' => $rec->id,
                        'user_id'            => $profesionalId,
                        'created_at'         => $now,
                        'updated_at'         => $now,
                    ];
                }

                DB::table('reception_user')->insert($pivotData);

                return redirect()->back()->with(
                    'success',
                    "Se han creado {$quantity} consecutivos exitosamente."
                );
            });
        } catch (\Exception $e) {
            Log::error('Error creating consecutives in SISCO: ' . $e->getMessage());

            return redirect()->back()->with(
                'error',
                'Error al crear los consecutivos: ' . $e->getMessage()
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        if (!$request->has('ids') || empty($request->input('ids'))) {
            return redirect()->back()->with('error', 'No se seleccionaron fichas para editar.');
        }

        if (count($request->input('ids')) > 20) {
            return redirect()->back()->with('error', 'No se pueden editar más de 20 fichas a la vez.');
        }

        session(['previous_url' => url()->previous()]);

        $files = Reception::with([
            'environment_file:id,entorno',
            'users' => function ($query) {
                $query->select('users.id', 'users.name', 'users.last_name')->distinct();
            },
            'localidad:id,name',
            'bases:id,name',
            'interventionType:id,name',
        ])->whereIn('id', $request->input('ids'))->get();

        $localidades = DB::table('localidades')->get();
        $formats = Format::select('id', 'name')->where('environment_id', $files->first()->environment)->get();

        return view('env.review-consecutives-edit', ['files' => $files, 'localidades' => $localidades, 'formats' => $formats]);
    }

    /**
     * Download csv file of the specified resource.
     */
    public function export(Request $request)
    {
        try {
            $files = Reception::with([
                'environment_file:id,entorno',
                'users' => function ($query) {
                    $query->select('users.id', 'users.name', 'users.last_name', 'role_id')->with('role')->distinct();
                }, //Profesionales
                'user:id,name,last_name', //Quien entregó
                'localidad:id,name',
                'bases:id,name',
                'interventionType:id,name',
                'productivity:id,file_id,observations',
                'status_histories' => function ($query) {
                    $query->select('id', 'reception_id', 'created_at')->where('new_status', 2)->orderBy('created_at', 'desc')->limit(1);
                }
            ])
                ->where('required_date', '>=', '2025-10-01 00:00:00');

            // --- Filtros dinámicos ---
            if ($request->filled('profesional')) {
                $files->whereHas('users', function ($query) use ($request) {
                    $query->where('user_id', $request->input('profesional'));
                });
            }

            if ($request->filled('environment')) {
                $files->where('environment', $request->input('environment'));
            }

            if ($request->filled('status')) {
                if ($request->input('status') == 3) {
                    $files->where(function ($query) {
                        $query->where('status', 4)->orWhere('status', 3);
                    });
                } else {
                    $files->where('status', $request->input('status'));
                }
            }

            if ($request->filled('ficha')) {
                $files->where('file_number', 'like', '%' . $request->input('ficha') . '%');
            }

            if ($request->filled('localidad') && is_array($request->input('localidad'))) {
                $files->whereIn('localidad_id', $request->input('localidad'));
            }

            if ($request->filled('month_intervention')) {
                $files->whereMonth('fecha_intervencion', $request->input('month_intervention'))
                    ->whereYear('fecha_intervencion', now()->year);
            }

            if ($request->filled('startDate') && $request->filled('endDate')) {
                $files->whereBetween('fecha_intervencion', [
                    Carbon::parse($request->input('startDate'))->startOfDay(),
                    Carbon::parse($request->input('endDate'))->endOfDay()
                ]);
            } elseif ($request->filled('startDate')) {
                $files->where('fecha_intervencion', '>=', Carbon::parse($request->input('startDate'))->startOfDay());
            } elseif ($request->filled('endDate')) {
                $files->where('fecha_intervencion', '<=', Carbon::parse($request->input('endDate'))->endOfDay());
            }

            $files = $files
                ->orderBy('consecutivo', 'ASC')
                ->orderBy('id', 'ASC')
                ->get();

            $header_titles = [
                'Número de ficha',
                'Formato',
                'Cantidad',
                'Fecha intervención',
                'Entorno',
                'Localidad',
                'Estado',
                'Fecha solicitud',
                'Fecha sellado',
                'Fecha entrega a GESI',
                'Quien entregó',
                'Observaciones',
                'Fecha devolución a entorno',
                'Perfil profesional (principal)',
                'Profesional(es)'
            ];

            $csv_file = new StreamedResponse(function () use ($files, $header_titles) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($handle, $header_titles, ';');

                foreach ($files as $row) {
                    $observation = '';
                    $stampDate = $row['status_histories'][0]['created_at'] ?? null;

                    if (isset($row->productivity)) {
                        $observation = !$row->productivity->observations ? '' : $row->productivity->observations;
                    }

                    if ($row->relationLoaded('statusHistories') && $row->statusHistories->isNotEmpty()) {
                        $stampDate = Carbon::parse($row->statusHistories->first()->created_at)->format('d/m/Y');
                    }

                    fputcsv($handle, [
                        $row->file_number,
                        $row->bases->name,
                        $row->quantity,
                        $row->fecha_intervencion ? Carbon::parse($row->fecha_intervencion)->format('d/m/Y') : '',
                        $row->environment_file->entorno,
                        $row->localidad->name,
                        getStatusNameFile($row->status),
                        $row->required_date ? Carbon::parse($row->required_date)->format('d/m/Y') : '',
                        $stampDate,
                        $row->user ? Carbon::parse($row->created_at)->format('d/m/Y') : '',
                        $row->user ? $row->user->name . ' ' . $row->user->last_name : '',
                        $observation,
                        $row->return_date ? Carbon::parse($row->return_date)->format('d/m/Y') : '',
                        $row->users->isNotEmpty() ? $row->users->first()->role->name : '',
                        implode(', ', $row->users->map(function ($user) {
                            return $user->name . ' ' . $user->last_name;
                        })->toArray()),
                    ], ';');
                }

                fclose($handle);
            });

            $csv_file->headers->set('Content-Type', 'text/csv; charset=UTF-8');
            $csv_file->headers->set('Content-Disposition', 'attachment; filename="Herramienta_de_control' . now()->format('YmdHis') . '.csv"');

            return $csv_file;
        } catch (\Exception $e) {
            Log::error('Error downloading review consecutives: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al exportar la herramienta de control');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {
            $fileNumbers = $request->input('file_numbers', []);
            $fileIds = $request->input('file_ids', []);
            $localidades = $request->input('localidad_id', []);
            $fechas = $request->input('fecha_intervencion', []);
            $formats = $request->input('format', []);

            $count = count($fileIds);
            if ($count !== count($localidades) || $count !== count($fechas) || $count !== count($formats)) {
                throw new \Exception('Los arrays no tienen la misma longitud.');
            }

            DB::transaction(function () use ($fileIds, $localidades, $fechas, $formats, $fileNumbers) {
                for ($i = 0; $i < count($fileIds); $i++) {
                    $file = Reception::find($fileIds[$i]);
                    if ($file) {
                        $file->update([
                            'file_number'        => $fileNumbers[$i],
                            'localidad_id'       => intval($localidades[$i]),
                            'fecha_intervencion' => $fechas[$i],
                            'format_id'          => $formats[$i],
                        ]);
                    }
                }
            });

            return redirect(session('previous_url', route('reviewconsecutives.index')))
                ->with('success', 'Fichas actualizadas correctamente.');
        } catch (\Exception $e) {
            Log::error('No se pudieron actualizar las fichas: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar las fichas.');
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $idsToUpdate = [];
            $idsToUpdate = explode(',', $request->input('selected_ids'));
            $statusInput = (int) $request->input('status');
            $maxBatchNumber = Reception::lockForUpdate()->max('batch_number');
            $maxBatchNumber = ($maxBatchNumber ?? 0) + 1;
            $authId = Auth::id();

            return DB::transaction(function () use ($idsToUpdate, $authId, $statusInput, $maxBatchNumber) {
                $historyData = [];
                $receptions = Reception::whereIn('id', $idsToUpdate)->get();
                $orderMap = array_flip($idsToUpdate);
                $newStatus = null;

                foreach ($receptions as $reception) {
                    // Cada ficha usa su propio status
                    $orderFile = null;
                    $status = $reception->status;

                    $statusEntrega = [2, 12, 13, 5, 14, 6, 7];

                    if (in_array($status, $statusEntrega)) {
                        $orderFile = $orderMap[$reception->id] + 1;
                    } else {
                        $maxBatchNumber = null;
                    }
                    // si el status no es sellado y tiene pacquete y ya fue devuelto
                    if ($status != 2 && $reception->package_id && $reception->received_by_environment) {
                        // Caso: Clonar
                        $newStatus = 3; // Entregado a GESI
                        $newReception = $reception->replicate();
                        $newReception->fill([
                            'status'                  => $newStatus,
                            'delivered_by'            => $authId,
                            'received_by'             => null,
                            'package_id'              => null,
                            'returned_by'             => null,
                            'return_date'             => null,
                            'received_by_environment' => null,
                            'order_file'              => $orderFile,
                            'batch_number'            => $maxBatchNumber,
                            'created_at'              => now(),
                        ]);
                        $newReception->save();
                        $reception->update([
                            'status' => 100, // Cerrada
                        ]);

                        // Clonar el registro de reception_user para el nuevo ID, solo el último usuario (profesional principal)
                        $mainUser = $reception->users()->latest()->first();
                        if ($mainUser) {
                            $newReception->users()->attach($mainUser->id);
                        }

                        $historyData[] = [
                            'reception_id'    => $newReception->id,
                            'previous_status' => $reception->status,
                            'new_status'      => $newStatus,
                            'changed_by'      => $authId,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    } else {

                        $newStatus = $status === 10 ? $status : ($status === 15 ? 3 : $status + 1);
                        // Cualquier otro status: actualización simple
                        $reception->update([
                            'status'       => $newStatus,
                            'delivered_by' => $authId,
                            'order_file'   => $orderFile,
                            'batch_number' => $maxBatchNumber,
                        ]);

                        $historyData[] = [
                            'reception_id'    => $reception->id,
                            'previous_status' => $reception->status,
                            'new_status'      => $newStatus,
                            'changed_by'      => $authId,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    }
                }

                ReceptionStatusHistory::insert($historyData);

                return redirect()->back()->with(
                    'success',
                    "Se han procesado " . count($idsToUpdate) . " fichas"
                );
            });
        } catch (\Exception $e) {
            Log::error("Error en updateStatus: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Error al actualizar el estado: ' . $e->getMessage());
        }
    }

    public function entregarGesi(Request $request)

    {
        try {

            $idsToUpdate = explode(',', $request->input('selected_ids'));
            $maxBatchNumber = Reception::lockForUpdate()->max('batch_number');
            $maxBatchNumber = ($maxBatchNumber ?? 0) + 1;

            $authId = Auth::id();

            return DB::transaction(function () use (
                $idsToUpdate,
                $authId,
                $maxBatchNumber,
            ) {

                $historyData = [];

                $receptions = Reception::whereIn('id', $idsToUpdate)->get();

                $orderMap = array_flip($idsToUpdate);

                foreach ($receptions as $reception) {

                    $orderFile = null;

                    $status = (int) $reception->status;
                    // 2 = sellado, 6 = entregado a profesional, 14recibido por entorno, entregado facilitador, 15 = correccionunteo
                    $statusEntrega = [2, 6, 14, 7, 15]; 

                    if (in_array($status, $statusEntrega)) {
                        $orderFile = $orderMap[$reception->id] + 1;
                    } else {
                        $maxBatchNumber = null;
                    }

                    // =====================================================
                    // CASO 1: SELLADO -> SOLO CAMBIA A 3
                    // =====================================================
                    if ($status === 2 || $status === 15) {

                        $newStatus = 3;

                        $reception->update([
                            'status'       => $newStatus,
                            'delivered_by' => $authId,
                            'order_file'   => $orderFile,
                            'batch_number' => $maxBatchNumber,
                        ]);

                        $historyData[] = [
                            'reception_id'    => $reception->id,
                            'previous_status' => $status,
                            'new_status'      => $newStatus,
                            'changed_by'      => $authId,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    }

                    // =====================================================
                    // CASO 2: ACTUALIZACIÓN -> CLONAR
                    // =====================================================
                    else if (
                        $reception->package_id &&
                        $reception->received_by_environment
                    ) {

                        $newStatus = 3;
                        $newReception = $reception->replicate();
                        $newReception->fill([
                            'status'                  => $newStatus,
                            'delivered_by'            => $authId,
                            'received_by'             => null,
                            'package_id'              => null,
                            'returned_by'             => null,
                            'return_date'             => null,
                            'received_by_environment' => null,
                            'order_file'              => $orderFile,
                            'batch_number'            => $maxBatchNumber,
                            'created_at'              => now(),
                        ]);

                        $newReception->save();

                        // Cerrar ficha anterior
                        $reception->update([
                            'status' => 100,
                        ]);

                        // Copiar usuario principal
                        $mainUser = $reception->users()->latest()->first();
                        if ($mainUser) {
                            $newReception->users()->attach($mainUser->id);
                        }

                        $historyData[] = [
                            'reception_id'    => $newReception->id,
                            'previous_status' => $status,
                            'new_status'      => $newStatus,
                            'changed_by'      => $authId,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    }
                }

                ReceptionStatusHistory::insert($historyData);

                return redirect()->back()->with(
                    'success',
                    "Se han procesado " . count($idsToUpdate) . " fichas"
                );
            });
        } catch (\Exception $e) {

            Log::error("Error en updateStatus: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with(
                'error',
                'Error al actualizar el estado: ' . $e->getMessage()
            );
        }
    }


    public function updateStatusSisco(Request $request)
    {
        try {
            $status = (int) $request->input('status');
            $idsToUpdate = explode(',', $request->input('selected_ids'));
            $authId = Auth::id();

            return DB::transaction(function () use ($status, $idsToUpdate, $authId) {
                $historyData = [];
                $receptions = ReceptionSisco::whereIn('id', $idsToUpdate)->get();
                $orderMap = array_flip($idsToUpdate);
                $maxBatchNumber = ReceptionSisco::lockForUpdate()->max('batch_number');
                $maxBatchNumber = ($maxBatchNumber ?? 0) + 1;

                foreach ($receptions as $reception) {
                    $orderFile = $orderMap[$reception->id] + 1;
                    $previousStatus = $reception->status;
                    $reception->update(['status' => 3, 'order_file' => $orderFile]);

                    $historyData[] = [
                        'reception_sisco_id' => $reception->id,
                        'previous_status' => $previousStatus,
                        'new_status' => 3,
                        'changed_by' => $authId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                ReceptionStatusHistory::insert($historyData);

                return redirect()->back()->with('success', count($idsToUpdate) . " consecutivos en proceso de entrega a GESI.");
            });
        } catch (\Exception $e) {
            Log::error("Error en updateStatusSisco: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Error al actualizar el estado: ' . $e->getMessage());
        }
    }

    protected function stringStatus($status)
    {
        switch ($status) {
            case '2':
                return 'aprobado';
            case '3':
                return 'entregado a GESI';
            case '10':
                return 'rechazado';
            case '6':
                return 'entregado a profesional';
            default:
                return 'desconocido';
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function getThirdBusinessDay(int $year, int $month): \Carbon\Carbon
    {
        $count = 0;
        $day = \Carbon\Carbon::create($year, $month, 1)->startOfDay();

        while (true) {
            if ($day->isWeekday()) {
                $count++;
                if ($count === 3) break;
            }
            $day->addDay();
        }

        return $day;
    }

    private function getFourthBusinessDay(int $year, int $month): \Carbon\Carbon
    {
        $count = 0;
        $day = \Carbon\Carbon::create($year, $month, 1)->startOfDay();

        while (true) {
            if ($day->isWeekday()) {
                $count++;
                if ($count === 4) break;
            }
            $day->addDay();
        }

        return $day;
    }

    private function getUserName($user)
    {
        if ($user === null) return null;
        return $user->name . ' ' . $user->last_name;
    }

    public function prepareDelivery()
    {
        return view('env.prepare-delivery');
    }

    // app/Http/Controllers/ReviewConsecutiveController.php
    public function searchFichas(Request $request)
    {
        $query = trim($request->input('query'));

        if ($query === '') {
            return response()->json(['fichas' => []]);
        }

        $today = now()->startOfDay();
        $thirdBusinessDay = $this->getThirdBusinessDay($today->year, $today->month);
        $withinGracePeriod = $today->lte($thirdBusinessDay);

        $currentYear  = $today->year;
        $currentMonth = $today->month;
        $prevMonthDate = $today->copy()->subMonth();
        $prevYear  = $prevMonthDate->year;
        $prevMonth = $prevMonthDate->month;

        // 🔹 TRAER SOLO ESTADOS VÁLIDOS
        $fichas = Reception::whereIn('status', [2, 6, 14, 7, 15])
            ->where(function ($q) use ($query) {

                $q->where('file_number', 'LIKE', "%{$query}%")
                    ->orWhereHas('users', function ($user) use ($query) {

                        $user->where(function ($u) use ($query) {

                            $u->where('name', 'LIKE', "%{$query}%")
                                ->orWhere('last_name', 'LIKE', "%{$query}%")
                                ->orWhereRaw(
                                    "CONCAT(name, ' ', last_name) LIKE ?",
                                    ["%{$query}%"]
                                )
                                ->orWhereRaw(
                                    "CONCAT(last_name, ' ', name) LIKE ?",
                                    ["%{$query}%"]
                                );
                        });
                    });
            })
            ->where(function ($q) use ($currentYear, $currentMonth, $prevYear, $prevMonth, $withinGracePeriod) {
                // Status 2 (Sellado): filtrar por fecha_intervencion del mes actual,
                // y también del mes anterior solo si aún estamos dentro de los 3 primeros días hábiles.
                $q->where(function ($sub) use ($currentYear, $currentMonth, $prevYear, $prevMonth, $withinGracePeriod) {
                    $sub->where('status', 2)
                        ->where(function ($dateQ) use ($currentYear, $currentMonth, $prevYear, $prevMonth, $withinGracePeriod) {
                            $dateQ->where(function ($curr) use ($currentYear, $currentMonth) {
                                $curr->whereYear('fecha_intervencion', $currentYear)
                                    ->whereMonth('fecha_intervencion', $currentMonth);
                            });
                            if ($withinGracePeriod) {
                                $dateQ->orWhere(function ($prev) use ($prevYear, $prevMonth) {
                                    $prev->whereYear('fecha_intervencion', $prevYear)
                                        ->whereMonth('fecha_intervencion', $prevMonth);
                                });
                            }
                        });
                })
                // Otros estados: sin restricción de fecha
                ->orWhereIn('status', [6, 14, 7, 15]);
            })
            ->with([
                'environment_file',
                'bases:id,name',
                'users:id,name,last_name'
            ])
            ->orderBy('id', 'desc')
            ->get();

        $fichasFiltradas = $fichas
            ->groupBy('file_number')
            ->flatMap(function ($group) {

                return $group
                    ->groupBy('format_id')
                    ->map(function ($subGroup) {

                        return $subGroup
                            ->sortByDesc('id')
                            ->first();
                    });
            })
            ->values();

        // 🔹 FORMATEO FINAL
        $results = $fichasFiltradas
            ->reject(fn($file) => $file->status == 10)
            ->values()
            ->map(function ($file) {

                $estados = [5, 6, 14, 7, 15];

                $statusLabel = in_array($file->status, $estados)
                    ? 'Actualización'
                    : ($file->status == 2 ? 'Sellado' : 'N/A');

                $statusClass = ($statusLabel == 'Sellado')
                    ? 'bg-success'
                    : 'bg-actualization';

                $rawName = $file->users
                    ->map(fn($u) => $u->name . ' ' . $u->last_name)
                    ->implode(', ');

                $formattedName = \Illuminate\Support\Str::title(
                    mb_strtolower($rawName)
                );

                return [
                    'id' => $file->id,
                    'file_number' => $file->file_number,
                    'environment_name' => $file->environment_file->entorno ?? 'N/A',
                    'base_name' => $file->bases->name ?? 'N/A',
                    'status_text' => $statusLabel,
                    'status_class' => $statusClass,
                    'user_full_name' => $formattedName ?: 'Sin usuario'
                ];
            });

        return response()->json([
            'fichas' => $results
        ]);
    }

    public function searchFichasSisco(Request $request)
    {
        $query = trim($request->input('query'));

        if ($query === '') {
            return response()->json(['fichas' => []]);
        }

        $fichas = ReceptionSisco::where(function ($q) use ($query) {
            $q->where('consecutivo_sisco', 'LIKE', "%{$query}%")
                ->orWhereHas('users', function ($user) use ($query) {
                    $user->where(function ($u) use ($query) {
                        $u->where('name', 'LIKE', "%{$query}%")
                            ->orWhere('last_name', 'LIKE', "%{$query}%")
                            ->orWhereRaw(
                                "CONCAT(name, ' ', last_name) LIKE ?",
                                ["%{$query}%"]
                            )
                            ->orWhereRaw(
                                "CONCAT(last_name, ' ', name) LIKE ?",
                                ["%{$query}%"]
                            );
                    });
                });
        })
            ->with([
                'environment',
                'format',
                'users:id,name,last_name'
            ])
            ->orderBy('id', 'desc')
            ->limit(100)
            ->get();

        $results = $fichas->map(function ($file) {
            $rawName = $file->users->map(fn($u) => $u->name . ' ' . $u->last_name)->implode(', ');
            $formattedName = \Illuminate\Support\Str::title(mb_strtolower($rawName));

            return [
                'id' => $file->id,
                'file_number' => $file->consecutivo_sisco,
                'environment_name' => $file->environment->entorno ?? 'N/A',
                'base_name' => $file->format->name ?? 'N/A',
                'status_text' => $file->status == 2 ? 'Sellado' : 'Actualización',
                'status_class' => $file->status == 2 ? 'bg-success' : 'bg-actualization',
                'user_full_name' => $formattedName ?: 'Sin usuario'
            ];
        });

        return response()->json(['fichas' => $results]);
    }
}
