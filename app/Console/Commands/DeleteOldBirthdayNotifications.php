<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\NotificacionCumpleaños;

class DeleteOldBirthdayNotifications extends Command
{
    protected $signature = 'birthdays:clean';
    protected $description = 'Elimina notificaciones de cumpleaños antiguas';

    public function handle()
    {
        $deleted = NotificacionCumpleaños::where('created_at', '<', Carbon::now()->subDay())
            ->delete();

        $this->info("Notificaciones eliminadas: " . $deleted);
    }
}