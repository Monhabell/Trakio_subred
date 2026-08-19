<?php

namespace App\Console\Commands;

// Modelos
use App\Models\ActasSemanales;
use App\Models\DataUser;
use App\Models\Document;
use App\Models\Entorno;
use App\Models\Notification;
use App\Models\Productivity;
use App\Models\User;
use App\Models\Format;
use App\Models\OtherTime;
use App\Models\Reception;
use App\Models\Productivity_sds;
use App\Models\ActaSignature;
use Illuminate\Support\Facades\DB;


// Librerías
use Carbon\Carbon;
use Spatie\Browsershot\Browsershot;

// Facades
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerarActasemanal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generar-actasemanal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $fechaInicio = Carbon::create(2025, 10, 1);
        // $fechaFin = Carbon::create(2026, 4, 16);

        $weeklyTasks = ActasSemanales::get();

        // for ($date = $fechaInicio; $date->lte($fechaFin); $date->addDay()) {

        //     // SOLO procesar si el día es Viernes (Friday)
        //     if ($date->format('l') !== 'Friday') {
        //         continue;
        //     }

        //     $this->info("Generando actas para el viernes: " . $date->toDateString());

        foreach ($weeklyTasks as $task) {


            if (!$task) {
                $this->error('No hay ninguna tarea programada');
                Log::error('No hay ninguna tarea programada');
                return;
            }

            $taskDays = $task->days;
            $today = strtolower(now()->format('l'));
            //$today = strtolower($date->format('l'));



            if (in_array($today, $taskDays)) {
                /**
                 * Imagen logo
                 **/
                $this->info('Generando actas para el entorno: ' . $task);

                $imagePath = public_path('img/logoNorte.min.png');
                $imageContents = file_get_contents($imagePath);
                $base64Image = base64_encode($imageContents);
                $htmlImage = '<img src="data:image/png;base64,' . $base64Image . '" alt="Logo norte" width="170" style="margin-top: 20px">';

                // consulta datos entrono

                $entorno = $task->environment_id;
                $fecha = Carbon::now()->toDateString(); // formato: YYYY-MM-DD
                //$fecha = $date->toDateString();
                $fechaActual = Carbon::now();
                //$fechaActual = $date->copy();
                $fechaInicial = $fechaActual->copy()->subDays(6);
                $fechaInicial2 = $fechaInicial->day;
                $fechaFinal = $fechaActual->day;
                $mes = $fechaActual->month;
                $year = $fechaActual->year;
                $day = $fechaActual->day;

                // traer digitadores del entorno
                $idEntorno = Entorno::where('id', $entorno)->first();

                // Los digitadores ya no están atados a un solo entorno (environment_assignment_id),
                // ahora digitan fichas de cualquier entorno. Por eso el acta de un entorno debe
                // incluir a quien realmente digitó algo de ese entorno esta semana, no a quien
                // tenga ese entorno asignado de forma fija en data_user.
                $digitadorIdsAntiguo = Productivity::whereBetween('productivity.created_at', [$fechaInicial, $fechaActual])
                    ->whereHas('receptions', function ($query) use ($entorno) {
                        $query->where('environment', $entorno);
                    })
                    ->pluck('dig_id');

                $digitadorIdsNuevo = Productivity_sds::whereBetween('created_at', [$fechaInicial, $fechaActual])
                    ->whereIn('file_number', function ($query) use ($entorno) {
                        $query->select('file_number')->from('receptions')->where('environment', $entorno);
                    })
                    ->pluck('dig_id');

                $digitadorIds = $digitadorIdsAntiguo->merge($digitadorIdsNuevo)->filter()->unique();

                $digitadores = DataUser::with('user')
                    ->whereIn('id_user', $digitadorIds)
                    ->whereHas('user', function ($query) use ($fechaInicial, $fechaActual) {
                        $query->where('role_id', 1)->where('is_active', 1);
                        $query->where('created_at', '<=', $fechaActual);
                        //$query->whereBetween('created_at', [$fechaInicial, $fechaActual]);
                    })
                    ->get();

                $datosProductividad = [];

                // Agrupar las cantidades por digitador
                $cantidadesAgrupadas = [];

                foreach ($digitadores as $digitador) {
                    // Consulta en la tabla Productividad para cada usuario
                    $productividad = Productivity::with('receptions', 'user', 'base')
                        ->where('dig_id', $digitador->user->id)
                        ->whereBetween('created_at', [$fechaInicial, $fechaActual])
                        ->where('observations', '!=', 'Ninguna')
                        ->get();

                    // Contar cuántos registros tiene este digitador
                    $cantidadRegistros = $productividad->count();

                    if ($cantidadRegistros > 0) {
                        $nombreDig = $productividad->first()->user->name;  // Obtener el nombre del digitador

                        // Agregar la cantidad de registros al array agrupado
                        if (isset($cantidadesAgrupadas[$nombreDig])) {
                            $cantidadesAgrupadas[$nombreDig] += $cantidadRegistros;
                        } else {
                            $cantidadesAgrupadas[$nombreDig] = $cantidadRegistros;
                        }

                        // Iterar sobre cada registro de productividad
                        foreach ($productividad as $item) {
                            $receptions = $item->receptions->file_number;
                            $formato = $item->base->name;
                            $observaciones = $item->observations;

                            // Agregar los datos a $datosProductividad
                            $datosProductividad[] = [
                                'nombreDig' => $nombreDig,
                                'receptions' => $receptions,
                                'formato' => $formato,
                                'observaciones' => $observaciones,
                                'cantidad' => $cantidadRegistros,
                                'nameCant' => $nombreDig,
                            ];
                        }
                    }
                }

                $horasProductividadPorDigitador = [];

                // 1. Configuración de Fecha de Corte
                $fechaCorte = Carbon::create(2026, 2, 10)->startOfDay();
                $horasProductividadPorDigitador = [];

                // Definimos el rango de la semana actual del bucle
                $inicioSemana = $fechaInicial; // Viene del cálculo anterior (subDays(6))
                $finSemana = $fechaActual->copy()->endOfDay();

                foreach ($digitadores as $digitador) {
                    $userId = $digitador->user->id;
                    $nombreDig = $digitador->user->name;
                    $totalMinutos = 0;

                    /* ==============================================
                    * 1️⃣ CÁLCULO ESQUEMA ANTIGUO (Antes del 10 de Feb)
                    * ============================================== */
                    if ($inicioSemana->lt($fechaCorte)) {
                        // Determinamos hasta qué punto consultamos en la tabla antigua
                        $finParaAntiguo = $finSemana->lt($fechaCorte) ? $finSemana : $fechaCorte->copy()->subSecond();

                        $minutosAntiguos = \App\Models\Productivity::where('dig_id', $userId)
                            ->whereBetween('productivity.created_at', [$inicioSemana, $finParaAntiguo])
                            ->leftJoin('formats', 'productivity.base_id', '=', 'formats.id')
                            ->select(DB::raw('SUM(formats.header_time + (productivity.quantity_users * formats.body_time)) as total'))
                            ->value('total') ?? 0;

                        $totalMinutos += $minutosAntiguos;
                    }

                    /* ==============================================
                    * 2️⃣ CÁLCULO ESQUEMA NUEVO SDS (Desde el 10 de Feb)
                    * ============================================== */
                    if ($finSemana->gte($fechaCorte)) {
                        // Determinamos desde qué punto consultamos en la tabla SDS
                        $inicioParaNuevo = $inicioSemana->gte($fechaCorte) ? $inicioSemana : $fechaCorte;

                        $minutosNuevos = \App\Models\Productivity_sds::where('dig_id', $userId)
                            ->whereBetween('created_at', [$inicioParaNuevo, $finSemana])
                            ->sum('calculation_time');

                        $totalMinutos += $minutosNuevos;
                    }

                    /* ==============================================
                    * 3️⃣ HORAS ADICIONALES (OtherTime) & ACCIONES
                    * ============================================== */
                    $horas_adicionales_minutos = \App\Models\OtherTime::where('id_user', $userId)
                        ->whereBetween('date_time', [$inicioSemana, $finSemana])
                        ->sum('time'); // Asumiendo que 'time' ya está en minutos, si son horas multiplica por 60

                    $totalHorasFinal = ($totalMinutos + $horas_adicionales_minutos) / 60;

                    // Obtener descripciones únicas para las acciones
                    $descripciones = \App\Models\OtherTime::where('id_user', $userId)
                        ->whereBetween('date_time', [$inicioSemana, $finSemana])
                        ->pluck('description')
                        ->unique()
                        ->join(', ');

                    // Guardamos en el array para el PDF
                    $horasProductividadPorDigitador[$nombreDig] = [
                        'horas' => $totalHorasFinal,
                        'acciones' => $descripciones ?: 'Sin acciones adicionales'
                    ];
                }

                $htmlContent = view('pdf/pdf_semanal', [
                    'entorno' => $entorno,
                    'entorno_name' => $idEntorno->entorno,
                    'title' => $entorno,
                    'content' => $entorno,
                    'fecha' => $fecha,
                    'fechaInicial' => $fechaInicial2,
                    'fechaFinal' => $fechaFinal,
                    'digitadores' => $digitadores,
                    'observaciones' => $datosProductividad,
                    'cantidadesAgrupadas' => $cantidadesAgrupadas,
                    'htmlImage' => $htmlImage,
                    'mes' => $mes,
                    'year' => $year,
                    'Horas_digitador' => $horasProductividadPorDigitador
                ])->render();

                $path = 'acts/informe_semanal/' . $year . '/' . $idEntorno->entorno . '/' . $mes . '/digitador';
                $filePath = $path . '/Digitadores' . $day . '.pdf';
                Storage::disk('public')->put($filePath, '');

                $fileAbsolutePath = Storage::disk('public')->path($filePath);

                Browsershot::html($htmlContent)
                    ->margins(20, 10, 20, 20)
                    ->save($fileAbsolutePath);

                // Crear documento y guardar instancia
                $document = Document::create([
                    'id_user' => 1,
                    'name' => 'report-weekly',
                    'path' => $filePath,
                    'document_mes' => $mes,
                    'file_year' => $year,
                    'status' => 0,
                    'email_to_send' => $task->email,
                ]);

                // Insert masivo de firmas
                $actaSignatures = [];

                foreach ($digitadores as $index => $digitador) {
                    $actaSignatures[] = [
                        'document_id' => $document->id,
                        'user_id'     => $digitador->user->id,
                        'order'       => $index + 1,
                        'is_signed'   => false,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }

                ActaSignature::insert($actaSignatures);


                //---------------------------------------------------------------------------------------------------
                //-----------------------------------------------------------------------------------------------------
                //-----------------------------------------------------------------------------------------------------
                //-----------------------------------------------------------------------------------------------------

                // generar acta tecnicos
                $cantidad_recibed_count = Reception::where('environment', $entorno)->whereBetween('created_at', [$fechaInicial, $fechaActual])->count();

                $cantidad_recibed = Reception::with('bases') // Cargar la relación bases (Format)
                    ->whereBetween('created_at', [$fechaInicial, $fechaActual])
                    ->where('environment', $entorno)
                    ->get()
                    ->groupBy('bases.name') // Agrupar por nombre del formato
                    ->map(function ($group) {
                        return [
                            'cantidad' => $group->count(),  // Cantidad de registros por formato
                            'detalles' => $group,          // Detalles de los registros en ese grupo
                        ];
                    });

                $cant_devuelta = Reception::where('environment', $entorno)->whereBetween('return_date', [$fechaInicial, $fechaActual])->count();

                $cantidad_dev = Reception::with('bases') // Cargar la relación bases (Format)
                    ->whereBetween('return_date', [$fechaInicial, $fechaActual])
                    ->where('environment', $entorno)
                    ->get()
                    ->groupBy('bases.name') // Agrupar por nombre del formato
                    ->map(function ($group) {
                        return [
                            'cantidad_dev' => $group->count(),  // Cantidad de registros por formato
                            'detalles_dev' => $group,          // Detalles de los registros en ese grupo
                        ];
                    });

                $Cant_creada = Reception::with('productivity', function ($query) use ($fechaInicial, $fechaActual) {
                    $query->whereBetween('created_at', [$fechaInicial, $fechaActual]);
                })
                    ->where('environment', $entorno)->count();

                $cantidad_cread = Reception::with('bases') // Cargar la relación bases (Format)
                    ->whereBetween('created_at', [$fechaInicial, $fechaActual])
                    ->where('environment', $entorno)
                    ->get()
                    ->groupBy('bases.name') // Agrupar por nombre del formato
                    ->map(function ($group) {
                        return [
                            'cantidad_cread' => $group->count(),  // Cantidad de registros por formato
                            'detalles_cread' => $group,          // Detalles de los registros en ese grupo
                        ];
                    });

                $firmas_final = [];


                $firmas = DataUser::with('user')
                    ->where('environment_assignment_id', $idEntorno->id)
                    ->whereHas('user', function ($query) {
                        $query->whereIn('role_id', [12, 10]);
                        $query->where('is_active', 1);
                    })
                    ->get();

                $hoy = Carbon::now();
                //$hoy = $date->copy();

                // Definimos los límites específicos
                $limiteFebrero2026 = Carbon::create(2026, 2, 28);
                $limiteMarzo2026 = Carbon::create(2026, 3, 19);

                if ($hoy->lt($limiteFebrero2026)) {
                    // Esto cubrirá TODO el 2025 y hasta el 27 de febrero de 2026
                    $userId = 176;
                } elseif ($hoy->gte($limiteMarzo2026)) {
                    // Esto cubrirá desde el 19 de marzo de 2026 en adelante
                    $userId = 49;
                } else {
                    // El periodo entre el 28 de feb y el 18 de marzo
                    $userId = null;
                }

                $firma_lider = null;

                if ($userId) {
                    $firma_lider = DataUser::with('user')
                        ->whereHas('user', function ($query) use ($userId) {
                            $query->where('id', $userId)
                                ->where('environment_id', 0);
                        })
                        ->get(); // mejor que get()
                }

                // Combinar ambas colecciones
                if ($firma_lider) {
                    $firmas_final = $firmas->merge($firma_lider);
                } else {
                    $firmas_final = $firmas;
                }

                $htmlContent_tecnicos = view('pdf/pdf_semanal_tecnicos', [
                    'entorno' => $entorno,
                    'entorno_name' => $idEntorno->entorno,
                    'title' => $entorno,
                    'content' => $entorno,
                    'fecha' => $fecha,
                    'fechaInicial' => $fechaInicial2,
                    'fechaFinal' => $fechaFinal,
                    'digitadores' => $digitadores,
                    'observaciones' => $datosProductividad,
                    'cantidadesAgrupadas' => $cantidadesAgrupadas,
                    'htmlImage' => $htmlImage,
                    'mes' => $mes,
                    'year' => $year,
                    'Horas_digitador' => $horasProductividadPorDigitador,
                    'cantidad_recibed' => $cantidad_recibed,
                    'cantidad_num' => $cantidad_recibed_count,
                    'cantidad_devuelta' => $cantidad_dev,
                    'cantidad_devuelta_num' => $cant_devuelta,
                    'cantidad_creada_H' => $cantidad_cread,
                    'cant_num_cread' => $Cant_creada,
                    'firmas_final' => $firmas_final
                ])->render();

                $path_tecnicos = 'acts/informe_semanal/' . $year . '/' . $idEntorno->entorno . '/' . $mes . '/Tecnicos';
                $filePath_tecnicos = $path_tecnicos . '/Tecnicos' . $day . '.pdf';
                Storage::disk('public')->put($filePath_tecnicos, '');
                $fileAbsolutePath_tecnicos = Storage::disk('public')->path($filePath_tecnicos);

                $document_tecnicos =  Document::create([
                    'id_user' => 1,
                    'name' => 'report-weekly',
                    'path' => $filePath_tecnicos, // Ruta relativa colocar 2 rutas
                    'document_mes' => $mes,
                    'file_year' => $year,
                    'status' => 0,
                    'email_to_send' => $task->email,
                ]);



                Browsershot::html($htmlContent_tecnicos)
                    ->margins(20, 10, 20, 20)
                    ->save($fileAbsolutePath_tecnicos);

                // Insert masivo de firmas
                $actaSignatures_tecnicos = [];

                foreach ($firmas_final as $index => $firm) {
                    $actaSignatures_tecnicos[] = [
                        'document_id' => $document_tecnicos->id,
                        'user_id'     => $firm->user->id,
                        'order'       => $index + 1,
                        'is_signed'   => false,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }

                ActaSignature::insert($actaSignatures_tecnicos);

                $tecnicos = User::whereIn('role_id', [7, 12])->where('environment_id', 0)->get();

                // Iterar sobre cada técnico
                foreach ($tecnicos as $tecnico) {
                    Notification::create([
                        'from_user_id' => $tecnico->id,  // ID del usuario que envía la notificación
                        'to_user_id' => $tecnico->id,  // ID del técnico al que va dirigida la notificación
                        'message' => 'Se han generado los informes semanales para el entorno ' . $idEntorno->entorno,
                        'type' => 'report-weekly',
                        'status' => 0,
                        'notifiable_type' => $filePath,
                        'notifiable_id' => $tecnico->id,
                        'other_time_id' => null,
                        'read_at' => null,
                        'data' => $task->email,
                    ]);
                }

                // Obtener las firmas de los usuarios con position_id 1, 2, y 4
                $telefone = DataUser::with('user')
                    ->where('environment_assignment_id', $idEntorno->id)
                    ->whereHas('user', function ($query) {
                        $query->whereIn('role_id', [10, 12, 7]);
                    })
                    ->first();

                $numbers = $telefone->phone;


                // foreach ($numbers as $number) {
                $params = array(
                    'token' => 'cioa6hiw0b0ly7ii',
                    'to' => $numbers,
                    'body' => 'Hola, soy Tara.
Espero que te encuentres muy bien. Te informo que las actas semanales del entorno ' . $idEntorno->entorno . ' se han generado con éxito. Ya puedes ingresar a la plataforma trakio.pro para proceder con su revisión, firma y aprobación.

Recuerda que, una vez aprobadas, se enviarán al correo gesisubrednorte@gmail.com a las 5:00 pm. Si no son aprobadas, el correo no será enviado.'
                );

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.ultramsg.com/instance102587/messages/chat",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => http_build_query($params),
                    CURLOPT_HTTPHEADER => array(
                        "content-type: application/x-www-form-urlencoded"
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);

                if ($err) {
                    echo "cURL Error #:" . $err;
                } else {
                    echo $response;
                }
                //}

            } else {
                $this->info('Para el dia actual no hay tarea programada: ' . $task);
            }
        }
        //}
    }
}
