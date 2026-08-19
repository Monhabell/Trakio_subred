<?php

namespace App\Console\Commands;

// Modelos
use App\Models\Reception;

// Mails
use App\Mail\Cant_vr_dig;

// Librerías
use Carbon\Carbon;

// Facades
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class recibe_vs_digitizer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:digitizer_recibe';

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
        // consultar cantidades de fecha a fecha 

        $fecha_actual = Carbon::now();

        function obtenerQuitoDiaHAbil($fecha){
            $dia_habil = 0;
            $dia = 1;

            while($dia_habil < 5){
                $fecha_actual = $fecha->copy()->startOfMonth()->addDays($dia- 1);
                if($fecha_actual->isWeekday()){
                    $dia_habil++;
                };
                $dia++;
            }

            return $fecha_actual;
        }


        $quinto_dia_habil = obtenerQuitoDiaHAbil($fecha_actual);

        // hacer la consulta en la base de daatos en la tabla receptions

        $Hogar = 0;
        $Laboral = 0;
        $Comunitario = 0;
        $Educativo = 0;
        $Institucional = 0;


        $dataRecep = Reception::select('enviroment', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$quinto_dia_habil->toDateString(), $fecha_actual])
            ->groupBy('enviroment')
            ->get();


        foreach ($dataRecep as $enviromentData) {
            if ($enviromentData->enviroment == 3) {
                $Educativo = $enviromentData->count;
            } elseif ($enviromentData->enviroment == 2) {
                $Laboral = $enviromentData->count;
            } elseif ($enviromentData->enviroment == 1) {
                $Hogar = $enviromentData->count;
            }elseif ($enviromentData->enviroment == 4) {
                $Comunitario = $enviromentData->count;
            }elseif ($enviromentData->enviroment == 6) {
                $Institucional = $enviromentData->count;
            }
        }


        Mail::to('mnonhabell@gmail.com')->send(new Cant_vr_dig(
        $Hogar, 
        $Laboral,
        $Comunitario,
        $Educativo,
        $Institucional));
    }

    
}
