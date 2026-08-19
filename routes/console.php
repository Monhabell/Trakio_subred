<?php

use App\Console\Commands\VerificarStatusActa;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\Birtdays;
use App\Console\Commands\GenerarActasemanal;
use App\Console\Commands\GenerarReporteGesi;
use App\Console\Commands\DeleteOldBirthdayNotifications;




Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command(Birtdays::class)->dailyAt('7:00'); // se ejecuta todos los dias a las 7 de la mañana
Schedule::command(GenerarActasemanal::class)->dailyAt('12:00'); // se ejecuta todos los dias 1 pm 
Schedule::command(VerificarStatusActa::class)->dailyAt('11:00'); // se ejcuta los vienes a las 5 de la tarde 
Schedule::command(VerificarStatusActa::class)->dailyAt('16:00'); // se ejcuta los vienes a las 5 de la tarde 
Schedule::command(DeleteOldBirthdayNotifications::class)->dailyAt('00:10');
Schedule::command(GenerarReporteGesi::class)->weeklyOn(5, '15:00'); // se ejecuta los viernes a las 12 del mediodía