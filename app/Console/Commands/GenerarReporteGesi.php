<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reception; // Tu modelo de recepciones
use App\Exports\avanceDigitacion;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Mail\ReporteGesiMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class GenerarReporteGesi extends Command
{
    protected $signature = 'reporte:gesi';
    protected $description = 'Genera el excel de avance de digitación GESI por entorno y lo envía por correo';

    public function handle()
    {
        $this->info('Iniciando generación de reporte...');

        // 1. Fechas de Producción (Mes Actual)
        $referencia = Carbon::now();
        $inicioMes = $referencia->copy()->startOfMonth();
        $finMes = $referencia->copy()->endOfMonth();

        // 2. Calcular los viernes del mes una sola vez
        $viernes = [];
        $tempFecha = $inicioMes->copy();
        while ($tempFecha->month == $inicioMes->month) {
            if ($tempFecha->isFriday()) {
                $viernes[] = $tempFecha->copy()->endOfDay();
            }
            $tempFecha->addDay();
        }

        // 3. Consulta de base de datos
        $recepciones = Reception::with(['bases', 'environment_file', 'status_histories'])
            ->whereBetween('fecha_intervencion', [$inicioMes, $finMes])
            ->whereHas('status_histories', function ($query) use ($inicioMes, $finMes) {
                $query->where('new_status', 11)
                    ->whereBetween('created_at', [$inicioMes, $finMes]);
            })
            ->get();

        $this->info("Registros encontrados: " . $recepciones->count());

        // 4. Procesar: Agrupar por Entorno -> Agrupar por Formulario -> Calcular Semanas
        $datosProcesados = $recepciones->groupBy(function ($item) {
            return $item->environment_file->entorno ?? 'N/A';
        })->map(function ($itemsPorEntorno) use ($inicioMes, $viernes) {

            return $itemsPorEntorno->groupBy('format_id')->map(function ($items) use ($inicioMes, $viernes) {
                $primer = $items->first();

                return (object)[
                    'entorno'    => $primer->environment_file->entorno ?? 'N/A',
                    'formulario' => $primer->bases->name ?? 'N/A',
                    'meta'       => $primer->bases->meta ?? 0,

                    'semana1' => $items->whereBetween('created_at', [$inicioMes->copy()->startOfMonth(), $viernes[0] ?? $inicioMes->copy()->endOfMonth()])->sum('quantity'),
                    'semana2' => isset($viernes[1]) ? $items->whereBetween('created_at', [$viernes[0]->copy()->addSecond(), $viernes[1]])->sum('quantity') : 0,
                    'semana3' => isset($viernes[2]) ? $items->whereBetween('created_at', [$viernes[1]->copy()->addSecond(), $viernes[2]])->sum('quantity') : 0,
                    'semana4' => isset($viernes[3]) ? $items->whereBetween('created_at', [$viernes[2]->copy()->addSecond(), $viernes[3]])->sum('quantity') : 0,
                    'semana5' => isset($viernes[4]) ? $items->whereBetween('created_at', [$viernes[3]->copy()->addSecond(), $inicioMes->copy()->endOfMonth()])->sum('quantity') : 0,
                ];
            });
        })->flatten(1)->sortBy('entorno');

        // 5. Guardar Archivo
        if (!Storage::disk('public')->exists('reportes_gesi')) {
            Storage::disk('public')->makeDirectory('reportes_gesi');
        }

        $fechaDescarga = now()->format('Y-m-d_H-i');
        $nombreArchivo = "reportes_gesi/Avance_GESI_{$fechaDescarga}.xlsx";

        Excel::store(new avanceDigitacion($datosProcesados), $nombreArchivo, 'public');
        $fullPath = storage_path('app/public/' . $nombreArchivo);

        // 6. Envío de Correo
        $destinatarios = [
            'gesinumeracion1@gmail.com',
            'gesisubrednorte@subrednorte.gov.co',
            'gesitecnico1@gmail.com',
            'prubiosp@gmail.com'
        ];

        try {
            Mail::to($destinatarios)->send(new ReporteGesiMail($fullPath));
            $this->info("Reporte enviado exitosamente a los destinatarios.");
        } catch (\Exception $e) {
            $this->error("Error al enviar el correo: " . $e->getMessage());
            Log::error("Fallo envío correo GESI: " . $e->getMessage());
        }
    }
}
