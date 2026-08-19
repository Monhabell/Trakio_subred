<?php

namespace App\Console\Commands;

use App\Mail\GesiDeliveryReminderMail;
use App\Models\Reception;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GesiDeliveryReminder extends Command
{
    protected $signature = 'gesi:delivery-reminder';
    protected $description = 'Envía recordatorio de entrega de bases GESI a todos los usuarios (excepto digitadores)';

    public function handle(): void
    {
        $deadlineDate = self::getSecondBusinessDayNextMonth();
        $deadlineDateFormatted = $deadlineDate->translatedFormat('l j \d\e F \d\e Y');
        $professionalDeadlineDateFormatted = self::getLastDayOfCurrentMonth()->translatedFormat('l j \d\e F \d\e Y');
        $currentMonthName = now()->translatedFormat('F Y');

        $pendingFichas = $this->buildPendingFichas();
        $fichasByUser  = $pendingFichas['by_user'];
        $fichasByCoord = $pendingFichas['by_coordinator'];

        // Todos los usuarios activos, excepto digitadores de GESI (environment_id=0 y is_admin=false)
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
                $fichas = $fichasByCoord[$user->id] ?? []; // profesionales que aprobó (mismo criterio que reception-delay-alerts)
            } else {
                $fichas = $fichasByUser[$user->id] ?? [];
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

        $this->info("Recordatorio enviado a {$sent} usuario(s). Fecha límite comunicada: {$deadlineDateFormatted}");
    }

    public static function getSecondBusinessDayNextMonth(): Carbon
    {
        $day = Carbon::now()->addMonth()->startOfMonth();
        $count = 0;

        while ($count < 2) {
            if (!$day->isWeekend()) {
                $count++;
            }
            if ($count < 2) {
                $day->addDay();
            }
        }

        return $day;
    }

    public static function getLastDayOfCurrentMonth(): Carbon
    {
        return Carbon::now()->endOfMonth()->startOfDay();
    }

    private function buildPendingFichas(): array
    {
        $today = now()->startOfDay();

        // Misma lógica que reception-delay-alerts: coordinador → profesionales que aprobó
        $coordToProfIds = \Illuminate\Support\Facades\DB::table('reception_user')
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

        $byUserId  = [];
        $byCoordId = [];

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

                foreach ($coordToProfIds as $coordId => $profIds) {
                    if (in_array($user->id, $profIds)) {
                        $byCoordId[$coordId][] = array_merge($row, [
                            'professional' => trim($user->name . ' ' . $user->last_name),
                        ]);
                    }
                }
            }
        }

        return ['by_user' => $byUserId, 'by_coordinator' => $byCoordId];
    }
}
