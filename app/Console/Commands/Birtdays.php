<?php

namespace App\Console\Commands;

// Notificaciones
use App\Notifications\BirthdayNotification;

// Modelos
use App\Models\DataUser;
use App\Models\User;

// Librerías
use Carbon\Carbon;

// Facades
use Illuminate\Console\Command;

class Birtdays extends Command
{
    /**
     * El nombre y la firma del comando.
     *
     * @var string
     */
    protected $signature = 'Birtdays';

    /**
     * La descripción del comando.
     *
     * @var string
     */
    protected $description = 'Mi tarea programada';

    /**
     * Ejecuta el comando.
     *
     * @return mixed
     */


    public function handle()
    {
        $today = Carbon::now()->format('m-d');
        $birthdayUsers = DataUser::whereRaw('DATE_FORMAT(birthdate, "%m-%d") = ?', [$today])->get();
        if ($birthdayUsers->isEmpty()) {
            $this->info('No birthdays today.');
            return;
        }
        $users = User::where('is_active', 1)->get(); // Obtener todos los usuarios
        
        if ($users->isEmpty()) {
            $this->info('No Hay');
            return;
        }

        foreach ($birthdayUsers as $birthday) {
        
            foreach ($users as $user) {
                $user->notify(new BirthdayNotification($birthday));
            }

        }
    }
}
