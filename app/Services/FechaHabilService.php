<?php

namespace App\Services;

use Yasumi\Yasumi;
use DateTime;

class FechaHabilService
{
    public static function obtenerSegundoDiaHabil(int $anio, int $mes): string
    {
        $festivos = Yasumi::create('Colombia', $anio);
        $contador = 0;
        $dia = 1;

        while (true) {
            $fecha = new DateTime("$anio-$mes-$dia");
            $esFinDeSemana = in_array($fecha->format('N'), [6, 7]);
            $esFestivo = $festivos->isHoliday($fecha);

            if (!$esFinDeSemana && !$esFestivo) {
                $contador++;
                if ($contador === 2) {
                    return $fecha->format('Y-m-d');
                }
            }

            $dia++;
            if ($dia > 31) break; // seguridad
        }

        return '';
    }

    public static function pasoSegundoDiaHabil(): bool
    {
        $hoy = new DateTime();
        $anio = (int) $hoy->format('Y');
        $mes = (int) $hoy->format('m');

        $segundoDiaHabil = self::obtenerSegundoDiaHabil($anio, $mes);
        return $hoy->format('Y-m-d') > $segundoDiaHabil;
    }
}
